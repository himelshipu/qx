# Database Schema & Relationships Audit & Fixes

**Date:** April 7, 2026
**Status:** Relationship Configuration Aligned with Migration Plan

---

## Summary of Changes

### 1. **Model Relationship Foreign Key Fixes**

Fixed explicit foreign key specifications in models where relationship method names don't match Laravel's default convention:

#### ✅ Models Updated:

| Model                      | Relationship   | Issue                                                     | Fix                                        |
| -------------------------- | -------------- | --------------------------------------------------------- | ------------------------------------------ |
| **InfluencerPortfolio**    | `influencer()` | Column: `creator_id`, Method: `influencer()`              | Added `'creator_id'` foreign key parameter |
| **InfluencerSocialLink**   | `influencer()` | Column: `creator_id`, Method: `influencer()`              | Added `'creator_id'` foreign key parameter |
| **InfluencerPlatformStat** | `influencer()` | Column: `creator_id`, Method: `influencer()`              | Added `'creator_id'` foreign key parameter |
| **Conversation**           | `creator()`    | Column: `creator_id` (to be renamed), Method: `creator()` | Added explicit foreign key parameter       |

### 2. **Influencer Model Relationship Fixes**

Updated the Influencer model's relationships to:

- Use explicit foreign keys for HasMany/HasOne relationships
- Use new pivot table names to match migration file

#### ✅ Changes:

```php
// Before:
public function socialLinks(): HasOne
{
    return $this->hasOne(InfluencerSocialLink::class);
}

// After:
public function socialLinks(): HasOne
{
    return $this->hasOne(InfluencerSocialLink::class, 'creator_id');
}
```

**All Influencer model relationships updated:**

- `socialLinks()` - Added explicit `'creator_id'`
- `portfolios()` - Added explicit `'creator_id'`
- `platformStats()` - Added explicit `'creator_id'`
- `categories()` - Pivot table: `'creator_categories'` → `'influencer_categories'`
- `badges()` - Pivot table: `'creator_badges'` → `'influencer_badges'`
- `acceptedOrders()` - Foreign key: `'accepted_for_creator_id'` → `'accepted_for_influencer_id'`

### 3. **Order Model Updates**

Updated to use new column names per migration:

- Fillable: `'accepted_for_creator_id'` → `'accepted_for_influencer_id'`
- Relationship: `acceptedForInfluencer()` uses `'accepted_for_influencer_id'`

### 4. **Conversation Model Updates**

Updated to use new column names per migration:

- Fillable: `'creator_id'` → `'influencer_id'`
- Relationship: `creator()` uses `'influencer_id'`

### 5. **Message Model Updates**

Updated to use new column names per migration:

- Fillable: `'on_behalf_of_creator_id'` → `'on_behalf_of_influencer_id'`
- Relationship: `onBehalfOfInfluencer()` uses `'on_behalf_of_influencer_id'`

---

## Current State - Pre-Migration

**⚠️ IMPORTANT:** These model updates are aligned with the migration file:

```
database/migrations/2026_04_07_000001_rename_creators_to_influencers.php
```

**Status:** Migration has NOT been executed yet.

### What This Means:

- ✅ Models are configured for the POST-MIGRATION state
- ❌ The actual database tables/columns haven't been renamed yet
- ⚠️ Code will fail until migration is run

---

## Database Tables & Columns Status

### Before Migration:

- Tables: `creators`, `creator_portfolios`, `creator_social_links`, `creator_platform_stats`, `creator_categories`, `creator_badges`
- Columns: `creator_id`, `accepted_for_creator_id`, `on_behalf_of_creator_id`

### After Migration (when run):

- Tables: `influencers`, `influencer_portfolios`, `influencer_social_links`, `influencer_platform_stats`, `influencer_categories`, `influencer_badges`
- Columns: `influencer_id`, `accepted_for_influencer_id`, `on_behalf_of_influencer_id`

---

## Foreign Key Relationships - Verified Correct

All BelongsTo relationships point to `Influencer::class`:

| Models with creator_id FK | Relationship Method         |
| ------------------------- | --------------------------- |
| CartItem                  | `creator()` → Influencer    |
| CampaignApplication       | `creator()` → Influencer    |
| CampaignInfluencer        | `creator()` → Influencer    |
| Conversation              | `creator()` → Influencer    |
| Message                   | (N/A for creator)           |
| ModeratorAssignment       | `creator()` → Influencer    |
| OrderItem                 | `creator()` → Influencer    |
| Package                   | `creator()` → Influencer    |
| PayoutAccount             | `creator()` → Influencer    |
| Payout                    | `creator()` → Influencer    |
| Review                    | `creator()` → Influencer    |
| SubOrder                  | `creator()` → Influencer    |
| InfluencerPortfolio       | `influencer()` → Influencer |
| InfluencerSocialLink      | `influencer()` → Influencer |
| InfluencerPlatformStat    | `influencer()` → Influencer |

---

## Next Steps

### ⚠️ CRITICAL: Run Migration Before Deployment

1. Ensure all code changes are committed
2. Run the migration:
    ```bash
    php artisan migrate
    ```
3. The migration will:
    - Rename all `creators` tables to `influencers`
    - Rename all `creator_*` columns to `influencer_*`
    - Update foreign key constraints
    - Provide rollback functionality if needed

### Validation After Migration

After running the migration, verify:

- [ ] All table names are correct
- [ ] All column names are correct
- [ ] Foreign key constraints are valid
- [ ] No foreign key errors in logs
- [ ] Application functions correctly with influencers

---

## Files Modified in This Session

1. ✅ `app/Models/InfluencerPortfolio.php` - Added explicit FK
2. ✅ `app/Models/InfluencerSocialLink.php` - Added explicit FK
3. ✅ `app/Models/InfluencerPlatformStat.php` - Added explicit FK
4. ✅ `app/Models/Influencer.php` - Multiple relationship fixes
5. ✅ `app/Models/Conversation.php` - Updated fillable & relationship
6. ✅ `app/Models/Order.php` - Updated fillable & relationship
7. ✅ `app/Models/Message.php` - Updated fillable & relationship

---

## Relationship Configuration Best Practices Implemented

✅ **Explicit Foreign Keys** - All non-conventional relationships now explicitly specify the foreign key
✅ **Consistent Naming** - All relationships now align with the new `influencer_*` naming scheme
✅ **Migration-Ready** - Models are configured for the post-migration database state
✅ **Pivot Table Names** - BelongsToMany relationships use correct pivot table names

---

## Testing Recommendations

After migration deployment:

1. **Test all relationships:**

    ```php
    // Should load influencer data
    $conversation->creator;
    $package->creator;
    $orderItem->creator;
    ```

2. **Test relationship methods:**

    ```php
    // Should work after migration
    $influencer->portfolios();
    $influencer->socialLinks;
    $influencer->campaigns;
    ```

3. **Verify foreign keys:**

    ```bash
    php artisan tinker
    >>> $influencer->acceptedOrders()->first();
    >>> $conversation->creator;
    ```

4. **Check database integrity:**
    - Verify no orphaned records
    - Check foreign key constraints are active
    - Validate referential integrity
