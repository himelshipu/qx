# Model Relationships - Fixes Applied

## Summary
Fixed all model relationships in the ROCKIES platform between User, Brand, Creator, Package, Campaign, and related models to ensure proper one-to-one, one-to-many, and many-to-many relationships with correct foreign key references.

## Changes Made

### 1. User Model (`app/Models/User.php`)
**Added:**
- `createdCampaigns()` → HasMany relationship to Campaign via `created_by` FK
  - Allows tracking campaigns created by admin/system users

**Existing relationships verified:**
- ✅ `brand()` → HasOne Brand
- ✅ `creator()` → HasOne Creator
- ✅ `createdPackages()` → HasMany Package via `created_by` FK
- ✅ `orders()` → HasMany Order via `buyer_user_id` FK
- ✅ `acceptedOrders()` → HasMany Order via `accepted_by_user_id` FK
- ✅ All RBAC relationships (roles, permissions)

### 2. Brand Model (`app/Models/Brand.php`)
**Fixed:**
- Corrected malformed syntax in user() relationship
- Added missing image path fields to $fillable array
  - `profile_image_path`
  - `cover_image_path`
- Enhanced casts() with proper datetime handling

**Verified relationships:**
- ✅ `user()` → BelongsTo User (one-to-one unique)
- ✅ `campaigns()` → HasMany Campaign
- ✅ `orders()` → HasMany Order
- ✅ `socialLinks()` → HasOne BrandSocialLink
- ✅ `billingProfile()` → HasOne BrandBillingProfile
- ✅ `onboardingProfile()` → HasOne BrandOnboardingProfile
- ✅ `reviews()` → HasMany Review

### 3. Creator Model (`app/Models/Creator.php`)
**Added:**
- `profile_image_path` to fillable array
- `cover_image_path` to fillable array
- Proper datetime casting for created_at/updated_at

**Verified relationships:**
- ✅ `user()` → BelongsTo User (one-to-one unique)
- ✅ `packages()` → HasMany Package
- ✅ `campaigns()` → BelongsToMany Campaign via campaign_applications pivot
- ✅ `campaignApplications()` → HasMany CampaignApplication (direct access)
- ✅ `socialLinks()` → HasOne CreatorSocialLink
- ✅ `platformStats()` → HasMany CreatorPlatformStat
- ✅ `categories()` → BelongsToMany Category via creator_categories
- ✅ `badges()` → BelongsToMany BadgeDefinition via creator_badges (with pivot data)
- ✅ `cartItems()` → HasMany CartItem
- ✅ `orderItems()` → HasMany OrderItem
- ✅ `conversations()` → HasMany Conversation
- ✅ `reviews()` → HasMany Review
- ✅ `portfolios()` → HasMany CreatorPortfolio
- ✅ `payoutAccounts()` → HasMany PayoutAccount
- ✅ `payouts()` → HasMany Payout
- ✅ `acceptedOrders()` → HasMany Order via accepted_for_creator_id FK

### 4. Campaign Model (`app/Models/Campaign.php`)
**Verified:**
- ✅ `brand()` → BelongsTo Brand
- ✅ `createdBy()` → BelongsTo User via `created_by` FK (already present)
- ✅ `creators()` → BelongsToMany Creator via campaign_applications pivot (with rich data)
- ✅ `applications()` → HasMany CampaignApplication (direct access)
- ✅ `targeting()` → HasOne CampaignTargeting
- ✅ `categories()` → BelongsToMany Category via campaign_categories
- ✅ `followerRanges()` → BelongsToMany FollowerRange via campaign_target_follower_ranges
- ✅ `assets()` → HasMany CampaignAsset
- ✅ `targetCountries()` → HasMany CampaignTargetCountry
- ✅ `cartItems()` → HasMany CartItem
- ✅ `orders()` → HasMany Order
- ✅ `orderItems()` → HasMany OrderItem

### 5. Package Model (`app/Models/Package.php`)
**Added:**
- Proper datetime casting for created_at/updated_at

**Verified relationships:**
- ✅ `creator()` → BelongsTo Creator
- ✅ `createdBy()` → BelongsTo User via `created_by` FK
- ✅ `cartItems()` → HasMany CartItem
- ✅ `orderItems()` → HasMany OrderItem

### 6. Order Model (`app/Models/Order.php`)
**Verified:**
- ✅ `buyer()` → BelongsTo User via `buyer_user_id` FK
- ✅ `brand()` → BelongsTo Brand
- ✅ `campaign()` → BelongsTo Campaign (nullable)
- ✅ `acceptedBy()` → BelongsTo User via `accepted_by_user_id` FK
- ✅ `acceptedForCreator()` → BelongsTo Creator via `accepted_for_creator_id` FK
- ✅ `items()` → HasMany OrderItem
- ✅ `conversations()` → HasMany Conversation
- ✅ `messages()` → HasMany OrderMessage
- ✅ `payments()` → HasMany Payment
- ✅ `statusHistory()` → HasMany OrderStatusHistory

### 7. CampaignApplication Model (`app/Models/CampaignApplication.php`)
**Status:** ✅ No changes needed - correctly implements pivot table relationships
- `campaign()` → BelongsTo Campaign
- `creator()` → BelongsTo Creator
- Pivot fields: status, pitch_message, proposed_rate, agreed_rate, applied_at, decided_at

## Database Foreign Key References Verified

| Table | Foreign Key | References | On Delete |
|-------|------------|-----------|-----------|
| brands | user_id | users.id | CASCADE |
| creators | user_id | users.id | CASCADE |
| packages | creator_id | creators.id | CASCADE |
| packages | created_by | users.id | NULL |
| campaigns | brand_id | brands.id | CASCADE |
| campaigns | created_by | users.id | NULL |
| campaign_applications | campaign_id | campaigns.id | CASCADE |
| campaign_applications | creator_id | creators.id | CASCADE |
| orders | buyer_user_id | users.id | CASCADE |
| orders | brand_id | brands.id | NULL |
| orders | campaign_id | campaigns.id | NULL |
| orders | accepted_by_user_id | users.id | NULL |
| orders | accepted_for_creator_id | creators.id | NULL |

## Relationship Integrity Checklist

✅ One-to-One Relationships (User ↔ Brand, User ↔ Creator)
✅ One-to-Many Relationships (Brand → Campaign, Creator → Package, etc.)
✅ Many-to-Many Relationships (Campaign ↔ Creator via campaign_applications)
✅ Cascading deletes properly configured
✅ Nullable foreign keys for optional relationships
✅ All models have proper casts for datetime fields
✅ All fillable arrays include necessary fields
✅ No orphaned foreign key references

## Testing
- ✅ All PHP files pass syntax validation
- ✅ Unit tests pass (framework bootstrap)
- ✅ No Eloquent relationship definition errors

## Files Modified
- `app/Models/User.php` - Added createdCampaigns() relationship
- `app/Models/Brand.php` - Fixed syntax, added image fields, proper casts
- `app/Models/Creator.php` - Added image fields, proper casts
- `app/Models/Campaign.php` - Verified all relationships (no changes needed)
- `app/Models/Package.php` - Added datetime casts
- `app/Models/Order.php` - Verified (no changes needed)
- `app/Models/CampaignApplication.php` - Verified (no changes needed)

## Reference Documentation
- See `RELATIONSHIP_GUIDE.md` for detailed relationship map and usage examples

