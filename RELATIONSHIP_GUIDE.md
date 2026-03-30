# Model Relationships Guide

## Core Relationship Map

### User Model
- **brand()** → HasOne `Brand` - User can own ONE brand
- **creator()** → HasOne `Creator` - User can be ONE creator
- **createdPackages()** → HasMany `Package` via `created_by` FK - Packages created by this user (admin/system user)
- **createdCampaigns()** → HasMany `Campaign` via `created_by` FK - Campaigns created by this user
- **orders()** → HasMany `Order` via `buyer_user_id` FK - Orders placed by this user (brand)
- **acceptedOrders()** → HasMany `Order` via `accepted_by_user_id` FK - Orders accepted (created user accepting)
- **acceptedOrderItems()** → HasMany `OrderItem` via `accepted_by_user_id` FK

### Brand Model
- **user()** → BelongsTo `User` - Brand belongs to ONE user (one-to-one unique)
- **campaigns()** → HasMany `Campaign` - Brand has many campaigns
- **orders()** → HasMany `Order` - Brand has many orders (as buyer)
- **socialLinks()** → HasOne `BrandSocialLink` - Brand has ONE social link profile
- **billingProfile()** → HasOne `BrandBillingProfile` - Brand has ONE billing profile
- **onboardingProfile()** → HasOne `BrandOnboardingProfile` - Brand has ONE onboarding profile
- **reviews()** → HasMany `Review` - Brand has many reviews

### Creator Model
- **user()** → BelongsTo `User` - Creator belongs to ONE user (one-to-one unique)
- **packages()** → HasMany `Package` - Creator has many service packages
- **campaigns()** → BelongsToMany `Campaign` via `campaign_applications` pivot table
  - Pivot fields: `status`, `pitch_message`, `proposed_rate`, `agreed_rate`, `applied_at`, `decided_at`
- **socialLinks()** → HasOne `CreatorSocialLink` - Creator has ONE social link profile
- **platformStats()** → HasMany `CreatorPlatformStat` - Creator stats per platform
- **categories()** → BelongsToMany `Category` via `creator_categories`
- **badges()** → BelongsToMany `BadgeDefinition` via `creator_badges` (with pivot `earned_at`, `is_active`)
- **campaignApplications()** → HasMany `CampaignApplication` - Direct access to applications
- **cartItems()** → HasMany `CartItem` - Items in carts referencing creator packages
- **orderItems()** → HasMany `OrderItem` - Order items for creator's packages
- **conversations()** → HasMany `Conversation` - Conversations with this creator
- **reviews()** → HasMany `Review` - Reviews for creator
- **portfolios()** → HasMany `CreatorPortfolio` - Creator portfolio items
- **payoutAccounts()** → HasMany `PayoutAccount` - Creator payout account details
- **payouts()** → HasMany `Payout` - Creator payouts
- **acceptedOrders()** → HasMany `Order` via `accepted_for_creator_id` FK

### Campaign Model
- **brand()** → BelongsTo `Brand` - Campaign belongs to ONE brand
- **createdBy()** → BelongsTo `User` via `created_by` FK - User who created the campaign
- **creators()** → BelongsToMany `Creator` via `campaign_applications` pivot table
  - Pivot fields: `status`, `pitch_message`, `proposed_rate`, `agreed_rate`, `applied_at`, `decided_at`
- **applications()** → HasMany `CampaignApplication` - Direct access to applications
- **targeting()** → HasOne `CampaignTargeting` - Campaign targeting rules
- **categories()** → BelongsToMany `Category` via `campaign_categories`
- **followerRanges()** → BelongsToMany `FollowerRange` via `campaign_target_follower_ranges`
- **assets()** → HasMany `CampaignAsset` - Campaign assets/attachments
- **targetCountries()** → HasMany `CampaignTargetCountry` - Target countries
- **cartItems()** → HasMany `CartItem` - Cart items for this campaign
- **orders()** → HasMany `Order` - Orders for this campaign
- **orderItems()** → HasMany `OrderItem` - Order items for this campaign

### Package Model
- **creator()** → BelongsTo `Creator` - Package belongs to ONE creator
- **createdBy()** → BelongsTo `User` via `created_by` FK - User who created/uploaded package
- **cartItems()** → HasMany `CartItem` - Cart items for this package
- **orderItems()** → HasMany `OrderItem` - Order items for this package

### CampaignApplication Model (Pivot)
- **campaign()** → BelongsTo `Campaign`
- **creator()** → BelongsTo `Creator`
- Status: invited, applied, shortlisted, approved, rejected, completed

### Order Model
- **buyer()** → BelongsTo `User` via `buyer_user_id` FK - User who placed order
- **brand()** → BelongsTo `Brand` - Brand being hired
- **campaign()** → BelongsTo `Campaign` - Campaign order is for (nullable)
- **acceptedBy()** → BelongsTo `User` via `accepted_by_user_id` FK - User who accepted order
- **acceptedForCreator()** → BelongsTo `Creator` via `accepted_for_creator_id` FK - Creator order is for
- **items()** → HasMany `OrderItem` - Order line items (packages selected)
- **conversations()** → HasMany `Conversation`
- **messages()** → HasMany `OrderMessage`
- **payments()** → HasMany `Payment`
- **statusHistory()** → HasMany `OrderStatusHistory`

## Key Relationship Rules

1. **User ↔ Brand/Creator**: ONE-TO-ONE relationship (users table has unique user_id in brands/creators)
2. **Brand → Campaigns**: ONE-TO-MANY (campaigns.brand_id FK)
3. **Creator → Packages**: ONE-TO-MANY (packages.creator_id FK)
4. **Campaign ↔ Creator**: MANY-TO-MANY via `campaign_applications` (includes status tracking)
5. **Order**: Contains both brand_id and campaign_id for order context
6. **CampaignApplication**: Acts as pivot table between Campaign and Creator with rich data

## Important Notes

- Package has `created_by` to track who created it (optional, for admin packages)
- Campaign has `created_by` to track campaign creator user
- Order's `accepted_by_user_id` is the USER who accepted (could be admin/moderator)
- Order's `accepted_for_creator_id` is the CREATOR the order is being accepted for
- All models use `cascadeOnDelete()` for referential integrity where appropriate

