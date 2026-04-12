<?php

namespace App\Models;

use App\Traits\HasPermissionsHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasPermissionsHelper;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'email',
        'password',
        'user_type',
        'verification_code',
        'verification_code_expires_at',
        'email_verified_at',
        'phone',
        'date_of_birth',
        'gender',
        'country',
        'city',
        'address_line',
        'postal_code',
        'company_name',
        'bio',
        'profile_image_path',
        'cover_image_path',
        'is_active',
        'last_login_at',
        'stripe_customer_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'            => 'datetime',
            'verification_code_expires_at' => 'datetime',
            'date_of_birth'                => 'date',
            'is_active'                    => 'boolean',
            'last_login_at'                => 'datetime',
            'password'                     => 'hashed'
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $user): void {
            $source     = $user->name ?: $user->email;
            $user->slug = static::buildUniqueSlug($source, null);
        });

        static::updating(function (self $user): void {
            if ($user->isDirty('name') || empty($user->slug)) {
                $source     = $user->name ?: $user->email;
                $user->slug = static::buildUniqueSlug($source, $user->id);
            }
        });

        // Prevent deletion of superadmin users
        static::deleting(function (self $user) {
            if ($user->isSuperadmin()) {
                throw new \Exception('Cannot delete superadmin users. They are protected.');
            }
        });
    }

    private static function buildUniqueSlug(?string $source, ?int $ignoreId): string
    {
        $base = Str::slug((string) $source);

        if ($base === '') {
            $base = 'user';
        }

        $slug   = $base;
        $suffix = 1;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId !== null, fn($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    public function brand(): HasOne
    {
        return $this->hasOne(Brand::class);
    }

    public function influencer(): HasOne
    {
        return $this->hasOne(Influencer::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')->withTimestamps();
    }

    public function createdPackages(): HasMany
    {
        return $this->hasMany(Package::class, 'created_by');
    }

    public function setProfileImagePathAttribute($value): void
    {
        $this->attributes['profile_image_path'] = $this->normalizeImagePathValue($value);
    }

    public function setCoverImagePathAttribute($value): void
    {
        $this->attributes['cover_image_path'] = $this->normalizeImagePathValue($value);
    }

    private function normalizeImagePathValue(mixed $value): ?string
    {
        if ($value === null || $value === false || $value === 0 || $value === '0') {
            return null;
        }

        $normalized = trim((string) $value);
        if ($normalized === '' || strtolower($normalized) === 'null') {
            return null;
        }

        return ltrim($normalized, '/');
    }

    public function createdCampaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'created_by');
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_user_id');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()->whereSlug($roleSlug)->exists();
    }

    /**
     * Check if user has superadmin role
     */
    public function hasSuperadminRole(): bool
    {
        return $this->roles()->where('is_superadmin', true)->exists();
    }

    /**
     * Check if user is a superadmin
     */
    public function isSuperadmin(): bool
    {
        return $this->hasSuperadminRole();
    }

    /**
     * Check if user can access dashboard (admin, moderator, or superadmin)
     * Brand and Influencer users cannot access dashboard
     */
    public function canAccessDashboard(): bool
    {
        // Based on user_type, not roles
        $dashboardUserTypes = ['admin', 'moderator', 'superadmin'];
        return in_array($this->user_type, $dashboardUserTypes);
    }

    /**
     * Check if user has a specific permission (directly or through roles)
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // Superadmin has all permissions
        if ($this->isSuperadmin()) {
            return true;
        }

        // Check direct user permissions
        if ($this->permissions()->whereSlug($permissionSlug)->exists()) {
            return true;
        }

        // Check role permissions
        return $this->roles()
            ->whereHas('permissions', fn($q) => $q->where('slug', $permissionSlug))
            ->exists();
    }

    /**
     * Assign a role to the user
     */
    public function assignRole(string|Role $role): void
    {
        if (is_string($role)) {
            $role = Role::whereSlug($role)->firstOrFail();
        }

        if (!$this->hasRole($role->slug)) {
            $this->roles()->attach($role);
        }
    }

    /**
     * Remove a role from the user
     */
    public function removeRole(Role $role): void
    {
        $this->roles()->detach($role);
    }

    /**
     * Sync roles for the user
     * 
     * IMPORTANT: Prevents superadmin role from being removed from superadmin users
     */
    public function syncRoles(array $roleIds): void
    {
        // If user is superadmin, prevent complete removal of superadmin role
        if ($this->isSuperadmin()) {
            $superadminRole = Role::where('is_superadmin', true)->first();
            if ($superadminRole && !in_array($superadminRole->id, $roleIds)) {
                // Ensure superadmin role is always in the sync
                $roleIds[] = $superadminRole->id;
            }
        }

        $this->roles()->sync($roleIds);
    }

    /**
     * Get all user permissions from roles
     */
    public function getAllPermissions()
    {
        return Permission::whereHas('roles', function ($query) {
            $query->whereIn('role_id', $this->roles()->pluck('role_id'));
        })->get();
    }

    public function acceptedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'accepted_by_user_id');
    }

    public function acceptedOrderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'accepted_by_user_id');
    }

    public function brandConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'brand_user_id');
    }

    public function handledConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'handled_by_user_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_user_id');
    }

    public function conversationParticipants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function uploadedDeliverables(): HasMany
    {
        return $this->hasMany(OrderDeliverable::class, 'uploaded_by_user_id');
    }

    public function statusHistoryChanges(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class, 'changed_by_user_id');
    }

    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'verification_code'            => null,
            'verification_code_expires_at' => null,
            'email_verified_at'            => $this->freshTimestamp()
        ])->save();
    }

    public function sendVerificationCodeNotification(): void
    {
        if ($this->verification_code && $this->verification_code_expires_at) {
            $timeSinceLastCode = now()->diffInSeconds($this->verification_code_expires_at->subMinutes(2));
            if ($timeSinceLastCode < 10) {
                return;
            }
        }

        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'verification_code'            => $verificationCode,
            'verification_code_expires_at' => now()->addMinutes(2)
        ]);

        \Illuminate\Support\Facades\Mail::send(
            new \App\Mail\SendVerificationCodeMail($this, $verificationCode)
        );
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->sendVerificationCodeNotification();
    }
}
