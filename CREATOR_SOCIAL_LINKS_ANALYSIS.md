# Creator Social Media Links - Complete Analysis

## Summary
Creator social media links are stored in the `creator_social_links` table with a `HasOne` relationship from the Creator model. The system is **properly configured for profile editing** but has a **critical bug during registration** where social links are attempted to be saved to the Creator model instead of the `creator_social_links` table.

---

## 1. Creator Registration Flow

### Registration Form
**File**: [resources/views/components/frontend/signup/creator.blade.php](resources/views/components/frontend/signup/creator.blade.php)

The creator registration form collects:
- `facebook` - Facebook URL
- `tiktok` - TikTok URL  
- `linkedin` - LinkedIn URL
- `instagram` - Instagram URL

These are optional fields presented in a "Social Links" section.

#### Relevant Form Code:
```html
<div>
    <label>Facebook</label>
    <input type="text" name="facebook" value="{{ old('facebook') }}" 
           placeholder="https://facebook.com/yourprofile" />
</div>
<div>
    <label>Tiktok</label>
    <input type="text" name="tiktok" value="{{ old('tiktok') }}" 
           placeholder="https://tiktok.com/@yourprofile" />
</div>
<div>
    <label>Linkedin</label>
    <input type="text" name="linkedin" value="{{ old('linkedin') }}" 
           placeholder="https://linkedin.com/in/yourprofile" />
</div>
<div>
    <label>Instagram</label>
    <input type="text" name="instagram" value="{{ old('instagram') }}" 
           placeholder="https://instagram.com/yourprofile" />
</div>
```

### Registration Controller - THE BUG
**File**: [app/Http/Controllers/Auth/RegisteredUserController.php](app/Http/Controllers/Auth/RegisteredUserController.php)

**Problem**: The `store()` method attempts to save social links directly to the Creator model using fields that don't exist in the model's fillable array.

#### Current (Buggy) Code:
```php
public function store(Request $request): RedirectResponse
{
    $request->validate([
        // ... other fields ...
        'facebook' => ['nullable', 'string', 'max:255'],
        'tiktok' => ['nullable', 'string', 'max:255'],
        'linkedin' => ['nullable', 'string', 'max:255'],
        'instagram' => ['nullable', 'string', 'max:255'],
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'user_type' => $request->user_type ?? 'brand',
    ]);

    if ($user->user_type === 'creator') {
        Creator::create([
            'user_id' => $user->id,
            'facebook' => $request->input('facebook'),      // ❌ DOESN'T EXIST IN FILLABLE
            'tiktok' => $request->input('tiktok'),          // ❌ DOESN'T EXIST IN FILLABLE
            'linkedin' => $request->input('linkedin'),      // ❌ DOESN'T EXIST IN FILLABLE
            'instagram' => $request->input('instagram'),    // ❌ DOESN'T EXIST IN FILLABLE
        ]);
    }

    // ...
}
```

**Impact**: These social media values are silently ignored because they're not in the Creator model's fillable array.

---

## 2. Creator Model Relationships

**File**: [app/Models/Creator.php](app/Models/Creator.php)

### Fillable Fields (Current):
```php
protected $fillable = [
    'user_id',
    'display_name',
    'title_name',
    'audience',
    'brands_worked_with',
    'is_active',
    'is_featured',
    'featured_priority'
];
```

**Note**: `facebook`, `tiktok`, `linkedin`, and `instagram` are **NOT** in the fillable array.

### Social Links Relationship:
```php
public function socialLinks(): HasOne
{
    return $this->hasOne(CreatorSocialLink::class);
}
```

This is a **HasOne** relationship, meaning:
- Each Creator has exactly ONE CreatorSocialLink record
- Accessed via `$creator->socialLinks` (singular relationship)

---

## 3. CreatorSocialLink Model

**File**: [app/Models/CreatorSocialLink.php](app/Models/CreatorSocialLink.php)

```php
class CreatorSocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'creator_id',
        'facebook_url',    // Note: _url suffix
        'instagram_url',   // Note: _url suffix
        'tiktok_url',      // Note: _url suffix
        'linkedin_url',    // Note: _url suffix
        'x_url',           // X (Twitter)
        'youtube_url',     // Note: _url suffix
        'other_url'        // Note: _url suffix
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }
}
```

**Important**: The fields use `_url` suffix (e.g., `facebook_url` instead of just `facebook`).

---

## 4. Profile Edit Form - PROPER IMPLEMENTATION

**File**: [resources/views/frontend/pages/creator-edit-profile.blade.php](resources/views/frontend/pages/creator-edit-profile.blade.php)

The edit page has a "Social Media" tab with the correct field names and accessing the relationship properly:

```blade
<div class="tab-content hidden" id="tab-social">
    <form id="socialForm" action="{{ route('dashboard.creator.profile.update', ['slug' => $slug]) }}" method="POST">
        @csrf
        
        <div>
            <label>Instagram</label>
            <input type="url" name="instagram_url" 
                   value="{{ old('instagram_url', $creator->socialLinks?->instagram_url ?? '') }}" 
                   placeholder="https://instagram.com/yourprofile" />
        </div>

        <div>
            <label>TikTok</label>
            <input type="url" name="tiktok_url" 
                   value="{{ old('tiktok_url', $creator->socialLinks?->tiktok_url ?? '') }}" 
                   placeholder="https://tiktok.com/@yourprofile" />
        </div>

        <div>
            <label>Facebook</label>
            <input type="url" name="facebook_url" 
                   value="{{ old('facebook_url', $creator->socialLinks?->facebook_url ?? '') }}" 
                   placeholder="https://facebook.com/yourprofile" />
        </div>

        <div>
            <label>X (Twitter)</label>
            <input type="url" name="x_url" 
                   value="{{ old('x_url', $creator->socialLinks?->x_url ?? '') }}" 
                   placeholder="https://x.com/yourprofile" />
        </div>

        <div>
            <label>YouTube</label>
            <input type="url" name="youtube_url" 
                   value="{{ old('youtube_url', $creator->socialLinks?->youtube_url ?? '') }}" 
                   placeholder="https://youtube.com/c/yourchannel" />
        </div>

        <div>
            <label>LinkedIn</label>
            <input type="url" name="linkedin_url" 
                   value="{{ old('linkedin_url', $creator->socialLinks?->linkedin_url ?? '') }}" 
                   placeholder="https://linkedin.com/in/yourprofile" />
        </div>

        <div>
            <label>Other Website</label>
            <input type="url" name="other_url" 
                   value="{{ old('other_url', $creator->socialLinks?->other_url ?? '') }}" 
                   placeholder="https://yourwebsite.com" />
        </div>

        <button type="submit">Save Social Links</button>
    </form>
</div>
```

**Key Points**:
- Uses null-safe accessor: `$creator->socialLinks?->instagram_url`
- Uses correct field names with `_url` suffix
- Properly references the relationship

---

## 5. Profile Edit Controller - PROPER RELATIONSHIP HANDLING

**File**: [app/Http/Controllers/CreatorProfileController.php](app/Http/Controllers/CreatorProfileController.php)

### Edit Method (Loading Relationship):
```php
public function edit(string $slug)
{
    $user = Auth::user();
    $creator = $this->getDashboardCreatorBySlug($slug);

    if (!$creator) {
        abort(404);
    }

    // ✅ Properly loads the socialLinks relationship
    $creator->load(['user', 'socialLinks', 'portfolios']);

    return view('frontend.pages.creator-edit-profile', [
        'user' => $creator->user,
        'creator' => $creator,
        'brand' => $creator,
        'slug' => $slug,
    ]);
}
```

### Update Method (Saving Social Links):
```php
public function update(Request $request, string $slug)
{
    $user = Auth::user();
    $creator = $this->getDashboardCreatorBySlug($slug);

    if (!$creator) {
        return redirect()->route('dashboard.creator.profile.edit')->with('error', 'Creator profile not found');
    }

    $validated = $request->validate([
        // ... other fields ...
        'instagram_url' => 'nullable|url|max:255',
        'tiktok_url' => 'nullable|url|max:255',
        'facebook_url' => 'nullable|url|max:255',
        'x_url' => 'nullable|url|max:255',
        'youtube_url' => 'nullable|url|max:255',
        'linkedin_url' => 'nullable|url|max:255',
        'other_url' => 'nullable|url|max:255',
    ]);

    // ... Update creator and user fields ...

    // ✅ Properly saves social links via the socialLinks relationship
    if (Schema::hasTable('creator_social_links')) {
        $creator->socialLinks()->updateOrCreate(
            ['creator_id' => $creator->id],
            [
                'instagram_url' => $validated['instagram_url'] ?? null,
                'tiktok_url' => $validated['tiktok_url'] ?? null,
                'facebook_url' => $validated['facebook_url'] ?? null,
                'x_url' => $validated['x_url'] ?? null,
                'youtube_url' => $validated['youtube_url'] ?? null,
                'linkedin_url' => $validated['linkedin_url'] ?? null,
                'other_url' => $validated['other_url'] ?? null,
            ]
        );
    }

    // ... Handle profile images, cover image, portfolio ...

    return redirect()->route('dashboard.creator.profile.edit', ['slug' => $slug])->with('success', 'Profile updated successfully.');
}
```

**Key Points**:
- Uses `updateOrCreate()` on the relationship
- Creates the CreatorSocialLink record if it doesn't exist
- Updates it if it does exist
- Uses correct field names with `_url` suffix

---

## 6. Public Profile Display

**File**: [app/Http/Controllers/CreatorProfileController.php](app/Http/Controllers/CreatorProfileController.php#L29)

```php
public function show(string $slug)
{
    $creator = Creator::query()
        ->with([
            'user',
            'categories:id,name',
            'portfolios' => fn($query) => $query->where('is_active', true)->orderBy('sort_order'),
            'platformStats' => fn($query) => $query
                ->where('is_active', true)
                ->orderByDesc('follower_count')
        ])
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->whereHas('user', function ($query) use ($slug): void {
            $query->where('slug', $slug)->where('user_type', 'creator');
        })
        ->firstOrFail();

    // Note: socialLinks relationship is NOT loaded for public profile display
    // This may need to be added if social links should be displayed publicly
}
```

**Note**: The `socialLinks` relationship is not being loaded for the public profile display. If social links should be displayed on creator profiles, this needs to be added to the with() array.

---

## Issues and Recommendations

### ❌ **Critical Issue #1: Registration Not Saving Social Links**

**Location**: [RegisteredUserController::store()](app/Http/Controllers/Auth/RegisteredUserController.php#L38-L68)

**Problem**: 
- Form collects `facebook`, `tiktok`, `linkedin`, `instagram`
- Controller tries to save them to Creator model with those field names
- Creator model doesn't have these fields in fillable array
- Data is silently discarded

**Solution**: 
After creating the Creator, also create the CreatorSocialLink record:

```php
if ($user->user_type === 'creator') {
    $creator = Creator::create([
        'user_id' => $user->id,
    ]);
    
    // Create the social links record
    CreatorSocialLink::create([
        'creator_id' => $creator->id,
        'facebook_url' => $request->input('facebook'),
        'tiktok_url' => $request->input('tiktok'),
        'linkedin_url' => $request->input('linkedin'),
        'instagram_url' => $request->input('instagram'),
    ]);
}
```

### ⚠️ **Issue #2: Field Name Mismatch**

The registration form uses simple names:
- `facebook`, `tiktok`, `linkedin`, `instagram`

But the edit form and database use:
- `facebook_url`, `tiktok_url`, `linkedin_url`, `instagram_url`

This inconsistency should be resolved. Either:
1. Rename registration form fields to match (`facebook_url`, etc.)
2. Or remap the values when saving

### ⚠️ **Issue #3: Public Profile Doesn't Load Social Links**

The public creator profile's `show()` method doesn't load the `socialLinks` relationship. If social links should be displayed publicly, add it to the with() array:

```php
$creator = Creator::query()
    ->with([
        'user',
        'socialLinks',  // Add this
        'categories:id,name',
        // ...
    ])
    // ...
```

### ⚠️ **Issue #4: Relationship Naming Inconsistency**

The Creator model has a `socialLinks()` relationship (singular return, plural method name). This is uncommon. Consider either:
- Renaming the relationship to `socialLink()` (singular, matches HasOne)
- Or changing to `HasMany` if creators can have multiple profiles

Currently it's valid but confusing to access as `$creator->socialLinks?>->instagram_url` rather than `$creator->socialLink?>->instagram_url`.

---

## 7. Data Flow Summary

### During Registration ❌ (Currently Broken)
```
Registration Form (name: "facebook", etc.)
    ↓
RegisteredUserController::store()
    ↓
Creator::create() [social fields silently ignored]
    ↓
Social links are LOST
```

### During Profile Edit ✅ (Works Correctly)
```
Edit Form (name: "instagram_url", etc.)
    ↓
CreatorProfileController::update()
    ↓
$creator->socialLinks()->updateOrCreate()
    ↓
CreatorSocialLink record created/updated
    ↓
Values correctly stored in creator_social_links table
```

### Public Profile Display ~
```
Creator query with load(['socialLinks'])
    ↓
Display values (if socialLinks relationship is loaded)
```

---

## 8. Database Schema

The `creator_social_links` table has:
- `id` (primary key)
- `creator_id` (foreign key to creators table)
- `facebook_url` (varchar)
- `instagram_url` (varchar)
- `tiktok_url` (varchar)
- `linkedin_url` (varchar)
- `x_url` (varchar)
- `youtube_url` (varchar)
- `other_url` (varchar)
- `created_at` (timestamp)
- `updated_at` (timestamp)

Each Creator has exactly ONE CreatorSocialLink record via the **HasOne** relationship.

---

## Action Items

1. **Fix Registration** - Make RegisteredUserController create CreatorSocialLink records
2. **Standardize Field Names** - Use consistent naming across registration and profile edit forms
3. **Load Relationship in Public Profile** - Add `socialLinks` to the public profile query
4. **Document Relationship Convention** - Clarify if singular HasOne should use singular method name
