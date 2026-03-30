<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
        'postal_code',
        'company_name',
        'job_title',
        'bio',
        'profile_image_path',
        'cover_image_path',
        'is_active',
        'last_login_at'
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

    public function creator(): HasOne
    {
        return $this->hasOne(Creator::class);
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

    public function uploadedDeliverables(): HasMany
    {
        return $this->hasMany(OrderDeliverable::class, 'uploaded_by_user_id');
    }

    public function statusHistoryChanges(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class, 'changed_by_user_id');
    }

    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->permissions()->where('slug', $permissionSlug)->exists()) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->hasPermission($permissionSlug)) {
                return true;
            }
        }

        return false;
    }

    public function assignRole(Role $role): void
    {
        if (!$this->hasRole($role->slug)) {
            $this->roles()->attach($role);
        }
    }

    public function removeRole(Role $role): void
    {
        $this->roles()->detach($role);
    }

    public function syncRoles(array $roleIds): void
    {
        $this->roles()->sync($roleIds);
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
