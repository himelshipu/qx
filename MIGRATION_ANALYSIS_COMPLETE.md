# Laravel Migration Analysis Report
**Generated:** April 7, 2026  
**Total Migrations Analyzed:** 74 files

---

## 1. DUPLICATE TIMESTAMP MIGRATIONS ⚠️

### CRITICAL ISSUES FOUND:

**Timestamp: `2026_03_29_000034` (DUPLICATE)**
- Migration #37: `2026_03_29_000034_create_messages_table.php` → Creates `messages` table
- Migration #38: `2026_03_29_000034_create_payments_table.php` → Creates `payments` table

**Action Required:** This duplicate timestamp will cause execution order conflicts. Rename one to ensure sequential ordering.

**Timestamp: `2026_03_30_000122` (DUPLICATE)**
- Migration #66: `2026_03_30_000122_add_payment_method_id_to_payments_table.php` → ALTER `payments`
- Migration #67: `2026_03_30_000122_add_stripe_customer_id_to_users_table.php` → ALTER `users`

**Action Required:** These modify different tables but share the same timestamp, causing execution order uncertainty.

---

## 2. TABLES CREATED (BY CREATION ORDER)

### Framework & System Tables (Created First)
1. **cache** | `0001_01_01_000000_create_cache_table.php`
2. **cache_locks** | `0001_01_01_000000_create_cache_table.php`
3. **jobs** | `0001_01_01_000000_create_jobs_table.php`
4. **job_batches** | `0001_01_01_000000_create_jobs_table.php`
5. **failed_jobs** | `0001_01_01_000000_create_jobs_table.php`

### Core Application Tables
6. **users** | `2026_03_29_000000_create_users_table.php`
7. **password_reset_tokens** | `2026_03_29_000001_create_password_reset_tokens_table.php`
8. **sessions** | `2026_03_29_000002_create_sessions_table.php`
9. **brands** | `2026_03_29_000003_create_brands_table.php`
10. **brand_social_links** | `2026_03_29_000004_create_brand_social_links_table.php`
11. **billing_profiles** | `2026_03_29_000005_create_billing_profiles_table.php`
12. **brand_onboarding_profiles** | `2026_03_29_000006_create_brand_onboarding_profiles_table.php`
13. **influencers** | `2026_03_29_000007_create_creators_table.php`
14. **influencer_social_links** | `2026_03_29_000008_create_creator_social_links_table.php`
15. **influencer_platform_stats** | `2026_03_29_000009_create_creator_platform_stats_table.php`
16. **categories** | `2026_03_29_000010_create_categories_table.php`
17. **influencer_categories** | `2026_03_29_000011_create_creator_categories_table.php`
18. **brand_onboarding_industries** | `2026_03_29_000012_create_brand_onboarding_industries_table.php`
19. **badge_definitions** | `2026_03_29_000013_create_badge_definitions_table.php`
20. **influencer_badges** | `2026_03_29_000014_create_creator_badges_table.php`
21. **influencer_portfolios** | `2026_03_29_000015_create_creator_portfolios_table.php`
22. **follower_ranges** | `2026_03_29_000016_create_follower_ranges_table.php`

### Campaign Management Tables
23. **campaigns** | `2026_03_29_000017_create_campaigns_table.php`
24. **campaign_targeting** | `2026_03_29_000018_create_campaign_targeting_table.php`
25. **campaign_categories** | `2026_03_29_000019_create_campaign_categories_table.php`
26. **campaign_target_countries** | `2026_03_29_000020_create_campaign_target_countries_table.php`
27. **campaign_target_follower_ranges** | `2026_03_29_000021_create_campaign_target_follower_ranges_table.php`
28. **campaign_assets** | `2026_03_29_000022_create_campaign_assets_table.php`
29. **campaign_applications** | `2026_03_29_000023_create_campaign_applications_table.php`

### E-Commerce Tables
30. **carts** | `2026_03_29_000024_create_carts_table.php`
31. **packages** | `2026_03_29_000025_create_packages_table.php`
32. **cart_items** | `2026_03_29_000026_create_cart_items_table.php`
33. **orders** | `2026_03_29_000027_create_orders_table.php`
34. **order_items** | `2026_03_29_000028_create_order_items_table.php`
35. **order_deliverables** | `2026_03_29_000029_create_order_deliverables_table.php`
36. **order_messages** | `2026_03_29_000030_create_order_messages_table.php`
37. **order_status_history** | `2026_03_29_000031_create_order_status_history_table.php`

### Communication Tables
38. **conversations** | `2026_03_29_000032_create_conversations_table.php`
39. **conversation_participants** | `2026_03_29_000033_create_conversation_participants_table.php`
40. **messages** | `2026_03_29_000034_create_messages_table.php`

### Payment Tables
41. **payments** | `2026_03_29_000034_create_payments_table.php`
42. **payout_accounts** | `2026_03_29_000035_create_payout_accounts_table.php`
43. **payouts** | `2026_03_29_000036_create_payouts_table.php`
44. **payout_items** | `2026_03_29_000037_create_payout_items_table.php`

### Review & Wishlist Tables
45. **reviews** | `2026_03_29_000038_create_reviews_table.php`
46. **wishlists** | `2026_03_29_000039_create_wishlists_table.php`
47. **wishlist_items** | `2026_03_29_000040_create_wishlist_items_table.php`

### CMS Tables
48. **pages** | `2026_03_29_000041_create_pages_table.php`
49. **page_sections** | `2026_03_29_000042_create_page_sections_table.php`
50. **case_studies** | `2026_03_29_000043_create_case_studies_table.php`
51. **testimonials** | `2026_03_29_000044_create_testimonials_table.php`
52. **featured_collaborations** | `2026_03_29_000045_create_featured_collaborations_table.php`

### FAQ & Support Tables
53. **faq_sections** | `2026_03_29_000046_create_faq_sections_table.php`
54. **faq_items** | `2026_03_29_000047_create_faq_items_table.php`
55. **support_categories** | `2026_03_29_000048_create_support_categories_table.php`
56. **support_articles** | `2026_03_29_000049_create_support_articles_table.php`
57. **support_questions** | `2026_03_29_000050_create_support_questions_table.php`
58. **support_tickets** | `2026_03_29_000051_create_support_tickets_table.php`
59. **support_ticket_messages** | `2026_03_29_000052_create_support_ticket_messages_table.php`
60. **support_ticket_attachments** | `2026_03_29_000053_create_support_ticket_attachments_table.php`

### Notification & Media Tables
61. **notifications** | `2026_03_29_000054_create_notifications_table.php`
62. **media_library** | `2026_03_29_000055_create_media_library_table.php`

### RBAC Tables
63. **roles** | `2026_03_29_000056_create_rbac_tables.php`
64. **permissions** | `2026_03_29_000056_create_rbac_tables.php`
65. **user_roles** | `2026_03_29_000056_create_rbac_tables.php`
66. **role_permissions** | `2026_03_29_000056_create_rbac_tables.php`
67. **user_permissions** | `2026_03_29_000056_create_rbac_tables.php`

### Campaign Influencer & Sub-Order Tables
68. **campaign_influencers** | `2026_03_29_000057_create_campaign_influencers_table.php`
69. **sub_orders** | `2026_03_29_000058_create_sub_orders_table.php`
70. **moderator_assignments** | `2026_03_29_000059_create_moderator_assignments_table.php`

### Knowledge Base & Payment Methods
71. **knowledge_base_articles** | `2026_03_30_000120_create_knowledge_base_articles_table.php`
72. **payment_methods** | `2026_03_30_000121_create_payment_methods_table.php`

---

## 3. INCREMENTAL MIGRATIONS (ALTER/ADD/DELETE ONLY)

These migrations modify existing tables rather than creating new ones.

| No. | Filename | Operation | Target Table(s) | Changes |
|-----|----------|-----------|-----------------|---------|
| 66 | `2026_03_30_000122_add_payment_method_id_to_payments_table.php` | ALTER | `payments` | ADD `payment_method_id` (FK to `payment_methods`) |
| 67 | `2026_03_30_000122_add_stripe_customer_id_to_users_table.php` | ALTER | `users` | ADD `stripe_customer_id` (unique, nullable) |
| 68 | `2026_04_05_062950_add_paid_at_to_order_items_table.php` | ALTER | `order_items` | ADD `paid_at` (nullable timestamp) |
| 69 | `2026_04_06_000001_add_public_id_to_conversations_table.php` | ALTER | `conversations` | ADD `public_id` (unique string, 32 chars) |
| 70 | `2026_04_06_000002_backfill_public_id_conversations.php` | DATA MIGRATION | `conversations` | UPDATE - backfill `public_id` values |
| 71 | `2026_04_06_000003_optimize_conversations_and_messages_indexes.php` | ALTER | `conversations`, `messages` | ADD multiple indexes for performance |
| 72 | `2026_04_07_000001_rename_creators_to_influencers.php` | RENAME | Multiple | Rename tables `creators_*` → `influencers_*`, rename columns `creator_id` → `influencer_id` |
| 73 | `2026_04_07_000002_update_user_type_creator_to_influencer.php` | UPDATE ENUM | `users` | Modify `user_type` enum: 'creator' → 'influencer' |
| 74 | `2026_04_07_000003_update_faq_sections_audience_type_to_influencer.php` | UPDATE ENUM | `faq_sections` | Modify `audience_type` enum: 'creator' → 'influencer' |

**Total Incremental Migrations:** 9

---

## 4. TABLE-BY-TABLE ANALYSIS WITH FINAL SCHEMA

### 1. **users**
- **Creation Migration:** `2026_03_29_000000_create_users_table.php`
- **Final Columns:**
  - `id` (INT, Primary Key)
  - `slug` (VARCHAR 255, UNIQUE)
  - `name` (VARCHAR 255)
  - `email` (VARCHAR 255, UNIQUE)
  - `password` (VARCHAR 255)
  - `user_type` (ENUM: 'brand', 'influencer', 'moderator', 'admin', DEFAULT 'brand') **[Modified in Migration #73]**
  - `phone` (VARCHAR 30, nullable)
  - `date_of_birth` (DATE, nullable)
  - `gender` (ENUM: 'male', 'female', 'other', nullable)
  - `country` (VARCHAR 120, nullable)
  - `city` (VARCHAR 120, nullable)
  - `address_line` (VARCHAR 255, nullable)
  - `postal_code` (VARCHAR 30, nullable)
  - `bio` (TEXT, nullable)
  - `profile_image_path` (VARCHAR 500, nullable)
  - `cover_image_path` (VARCHAR 500, nullable)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `verification_code` (VARCHAR 20, nullable)
  - `verification_code_expires_at` (TIMESTAMP, nullable)
  - `email_verified_at` (TIMESTAMP, nullable)
  - `last_login_at` (TIMESTAMP, nullable)
  - `stripe_customer_id` (VARCHAR 255, UNIQUE, nullable) **[Added in Migration #67]**
  - `remember_token` (VARCHAR 100, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations Affecting This Table:**
  - #67: `2026_03_30_000122_add_stripe_customer_id_to_users_table.php` (ADD column)
  - #73: `2026_04_07_000002_update_user_type_creator_to_influencer.php` (UPDATE enum values)

---

### 2. **cache**
- **Creation Migration:** `0001_01_01_000000_create_cache_table.php`
- **Final Columns:**
  - `key` (VARCHAR 255, Primary Key)
  - `value` (MEDIUMTEXT)
  - `expiration` (INT)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 3. **cache_locks**
- **Creation Migration:** `0001_01_01_000000_create_cache_table.php`
- **Final Columns:**
  - `key` (VARCHAR 255, Primary Key)
  - `owner` (VARCHAR 255)
  - `expiration` (INT)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 4. **jobs**
- **Creation Migration:** `0001_01_01_000000_create_jobs_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `queue` (VARCHAR 255, Indexed)
  - `payload` (LONGTEXT)
  - `attempts` (TINYINT UNSIGNED)
  - `reserved_at` (INT UNSIGNED, nullable)
  - `available_at` (INT UNSIGNED)
  - `created_at` (INT UNSIGNED)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 5. **job_batches**
- **Creation Migration:** `0001_01_01_000000_create_jobs_table.php`
- **Final Columns:**
  - `id` (VARCHAR 255, Primary Key)
  - `name` (VARCHAR 255)
  - `total_jobs` (INT)
  - `pending_jobs` (INT)
  - `failed_jobs` (INT)
  - `failed_job_ids` (LONGTEXT)
  - `options` (MEDIUMTEXT, nullable)
  - `cancelled_at` (INT, nullable)
  - `created_at` (INT)
  - `finished_at` (INT, nullable)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 6. **failed_jobs**
- **Creation Migration:** `0001_01_01_000000_create_jobs_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `uuid` (VARCHAR 255, UNIQUE)
  - `connection` (TEXT)
  - `queue` (TEXT)
  - `payload` (LONGTEXT)
  - `exception` (LONGTEXT)
  - `failed_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 7. **password_reset_tokens**
- **Creation Migration:** `2026_03_29_000001_create_password_reset_tokens_table.php`
- **Final Columns:**
  - `email` (VARCHAR 255, Primary Key)
  - `token` (VARCHAR 255)
  - `created_at` (TIMESTAMP, nullable)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 8. **sessions**
- **Creation Migration:** `2026_03_29_000002_create_sessions_table.php`
- **Final Columns:**
  - `id` (VARCHAR 255, Primary Key)
  - `user_id` (BIGINT UNSIGNED, nullable, Indexed, FK→users, nullOnDelete)
  - `ip_address` (VARCHAR 45, nullable)
  - `user_agent` (TEXT, nullable)
  - `payload` (LONGTEXT)
  - `last_activity` (INT, Indexed)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 9. **brands**
- **Creation Migration:** `2026_03_29_000003_create_brands_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `user_id` (BIGINT UNSIGNED, UNIQUE, FK→users, cascadeOnDelete)
  - `brand_name` (VARCHAR 255)
  - `industry` (VARCHAR 150, nullable)
  - `website` (VARCHAR 500, nullable)
  - `is_verified` (BOOLEAN, DEFAULT false)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 10. **brand_social_links**
- **Creation Migration:** `2026_03_29_000004_create_brand_social_links_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `brand_id` (BIGINT UNSIGNED, FK→brands, cascadeOnDelete)
  - `facebook_url` (VARCHAR 500, nullable)
  - `instagram_url` (VARCHAR 500, nullable)
  - `tiktok_url` (VARCHAR 500, nullable)
  - `linkedin_url` (VARCHAR 500, nullable)
  - `x_url` (VARCHAR 500, nullable)
  - `youtube_url` (VARCHAR 500, nullable)
  - `other_url` (VARCHAR 500, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 11. **billing_profiles**
- **Creation Migration:** `2026_03_29_000005_create_billing_profiles_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `user_id` (BIGINT UNSIGNED)
  - `user_type` (ENUM: 'brand', 'creator')
  - `legal_company_name` (VARCHAR 255, nullable)
  - `vat_id` (VARCHAR 120, nullable)
  - `billing_address` (VARCHAR 255, nullable)
  - `billing_city` (VARCHAR 120, nullable)
  - `billing_country` (VARCHAR 120, nullable)
  - `billing_postal_code` (VARCHAR 30, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Index:** `['user_type', 'user_id']`
- **Incremental Migrations:** None
- **Status:** No alterations
- **⚠️ NOTE:** This table has `user_type` enum with old 'creator' value (not updated in Migration #73)

---

### 12. **brand_onboarding_profiles**
- **Creation Migration:** `2026_03_29_000006_create_brand_onboarding_profiles_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `brand_id` (BIGINT UNSIGNED, FK→brands, cascadeOnDelete)
  - `objective` (VARCHAR 120, nullable)
  - `budget_range` (VARCHAR 120, nullable)
  - `business_type` (VARCHAR 120, nullable)
  - `company_size` (VARCHAR 120, nullable)
  - `is_completed` (BOOLEAN, DEFAULT false)
  - `completed_at` (TIMESTAMP, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 13. **influencers** (formerly `creators`)
- **Creation Migration:** `2026_03_29_000007_create_creators_table.php` (note: file name says "creators" but creates "influencers")
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `user_id` (BIGINT UNSIGNED, UNIQUE, FK→users, cascadeOnDelete)
  - `display_name` (VARCHAR 255, nullable)
  - `title_name` (VARCHAR 255, nullable)
  - `audience` (TEXT, nullable)
  - `brands_worked_with` (TEXT, nullable)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `is_featured` (BOOLEAN, DEFAULT false)
  - `featured_priority` (SMALLINT UNSIGNED, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** 
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (table rename - no schema change)
- **Status:** Table was renamed in final migration but schema unchanged

---

### 14. **influencer_social_links** (formerly `creator_social_links`)
- **Creation Migration:** `2026_03_29_000008_create_creator_social_links_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `influencer_id` (BIGINT UNSIGNED, FK→influencers, cascadeOnDelete) **[Column renamed from `creator_id` in #72]**
  - `facebook_url` (VARCHAR 500, nullable)
  - `instagram_url` (VARCHAR 500, nullable)
  - `tiktok_url` (VARCHAR 500, nullable)
  - `youtube_url` (VARCHAR 500, nullable)
  - `linkedin_url` (VARCHAR 500, nullable)
  - `x_url` (VARCHAR 500, nullable)
  - `other_url` (VARCHAR 500, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:**
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (table rename + column rename)

---

### 15. **influencer_platform_stats** (formerly `creator_platform_stats`)
- **Creation Migration:** `2026_03_29_000009_create_creator_platform_stats_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `influencer_id` (BIGINT UNSIGNED, FK→influencers, cascadeOnDelete) **[Renamed in #72]**
  - `platform` (ENUM: 'instagram', 'tiktok', 'youtube', 'linkedin', 'facebook', 'x', 'twitch', 'ugc', 'other')
  - `handle` (VARCHAR 255, nullable)
  - `profile_url` (VARCHAR 500, nullable)
  - `follower_count` (BIGINT UNSIGNED, nullable)
  - `avg_views` (BIGINT UNSIGNED, nullable)
  - `engagement_rate` (DECIMAL 5,2, nullable)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Unique Constraint:** `['influencer_id', 'platform']`
- **Incremental Migrations:**
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (table + column rename)

---

### 16. **categories**
- **Creation Migration:** `2026_03_29_000010_create_categories_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `name` (VARCHAR 150)
  - `slug` (VARCHAR 180, UNIQUE)
  - `description` (TEXT, nullable)
  - `icon_path` (VARCHAR 500, nullable)
  - `image_path` (VARCHAR 500, nullable)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `is_featured` (BOOLEAN, DEFAULT false)
  - `featured_order` (INT, nullable)
  - `sort_order` (INT, DEFAULT 0)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 17. **influencer_categories** (formerly `creator_categories`)
- **Creation Migration:** `2026_03_29_000011_create_creator_categories_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `influencer_id` (BIGINT UNSIGNED, FK→influencers, cascadeOnDelete) **[Renamed in #72]**
  - `category_id` (BIGINT UNSIGNED, FK→categories, cascadeOnDelete)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Unique Constraint:** `['influencer_id', 'category_id']`
- **Incremental Migrations:**
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (table + column rename)

---

### 18. **brand_onboarding_industries**
- **Creation Migration:** `2026_03_29_000012_create_brand_onboarding_industries_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `brand_onboarding_profile_id` (BIGINT UNSIGNED, FK→brand_onboarding_profiles, cascadeOnDelete)
  - `category_id` (BIGINT UNSIGNED, FK→categories, cascadeOnDelete)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Unique Constraint:** `['brand_onboarding_profile_id', 'category_id']` (named: `brand_onboarding_profile_category_unique`)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 19. **badge_definitions**
- **Creation Migration:** `2026_03_29_000013_create_badge_definitions_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `code` (VARCHAR 120, UNIQUE)
  - `name` (VARCHAR 120)
  - `description` (TEXT, nullable)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 20. **influencer_badges** (formerly `creator_badges`)
- **Creation Migration:** `2026_03_29_000014_create_creator_badges_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `influencer_id` (BIGINT UNSIGNED, FK→influencers, cascadeOnDelete) **[Renamed in #72]**
  - `badge_definition_id` (BIGINT UNSIGNED, FK→badge_definitions, cascadeOnDelete)
  - `earned_at` (TIMESTAMP, nullable)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Unique Constraint:** `['influencer_id', 'badge_definition_id']`
- **Incremental Migrations:**
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (table + column rename)

---

### 21. **influencer_portfolios** (formerly `creator_portfolios`)
- **Creation Migration:** `2026_03_29_000015_create_creator_portfolios_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `influencer_id` (BIGINT UNSIGNED, FK→influencers, cascadeOnDelete) **[Renamed in #72]**
  - `media_type` (ENUM: 'image', 'video', DEFAULT 'image')
  - `file_path` (VARCHAR 500)
  - `title` (VARCHAR 255, nullable)
  - `description` (TEXT, nullable)
  - `sort_order` (INT, DEFAULT 0)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:**
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (table + column rename)

---

### 22. **follower_ranges**
- **Creation Migration:** `2026_03_29_000016_create_follower_ranges_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `code` (VARCHAR 80, UNIQUE)
  - `label` (VARCHAR 120)
  - `min_followers` (BIGINT UNSIGNED, nullable)
  - `max_followers` (BIGINT UNSIGNED, nullable)
  - `sort_order` (INT, DEFAULT 0)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 23. **campaigns**
- **Creation Migration:** `2026_03_29_000017_create_campaigns_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `brand_id` (BIGINT UNSIGNED, FK→brands, cascadeOnDelete)
  - `created_by` (BIGINT UNSIGNED, nullable, FK→users, nullOnDelete)
  - `title` (VARCHAR 255)
  - `campaign_type` (ENUM: 'facebook', 'instagram', 'tiktok', 'linkedin', 'x', 'youtube', 'ugc', 'other')
  - `description` (TEXT, nullable)
  - `instructions` (LONGTEXT, nullable)
  - `status` (ENUM: 'draft', 'published', 'paused', 'closed', 'archived', DEFAULT 'draft')
  - `budget_min` (DECIMAL 12,2, nullable)
  - `budget_max` (DECIMAL 12,2, nullable)
  - `currency` (CHAR 3, DEFAULT 'USD')
  - `start_date` (DATE, nullable)
  - `end_date` (DATE, nullable)
  - `published_at` (TIMESTAMP, nullable)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 24. **campaign_targeting**
- **Creation Migration:** `2026_03_29_000018_create_campaign_targeting_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `campaign_id` (BIGINT UNSIGNED, FK→campaigns, cascadeOnDelete)
  - `influencer_count` (INT UNSIGNED, nullable)
  - `target_gender` (ENUM: 'any', 'male', 'female', 'other', DEFAULT 'any')
  - `age_min` (TINYINT UNSIGNED, nullable)
  - `age_max` (TINYINT UNSIGNED, nullable)
  - `notes` (TEXT, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 25. **campaign_categories**
- **Creation Migration:** `2026_03_29_000019_create_campaign_categories_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `campaign_id` (BIGINT UNSIGNED, FK→campaigns, cascadeOnDelete)
  - `category_id` (BIGINT UNSIGNED, FK→categories, cascadeOnDelete)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Unique Constraint:** `['campaign_id', 'category_id']` (named: `campaign_category_unique`)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 26. **campaign_target_countries**
- **Creation Migration:** `2026_03_29_000020_create_campaign_target_countries_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `campaign_id` (BIGINT UNSIGNED, FK→campaigns, cascadeOnDelete)
  - `country_code` (CHAR 2)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Unique Constraint:** `['campaign_id', 'country_code']` (named: `campaign_country_unique`)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 27. **campaign_target_follower_ranges**
- **Creation Migration:** `2026_03_29_000021_create_campaign_target_follower_ranges_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `campaign_id` (BIGINT UNSIGNED, FK→campaigns, cascadeOnDelete)
  - `follower_range_id` (BIGINT UNSIGNED, FK→follower_ranges, cascadeOnDelete)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Unique Constraint:** `['campaign_id', 'follower_range_id']` (named: `campaign_follower_range_unique`)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 28. **campaign_assets**
- **Creation Migration:** `2026_03_29_000022_create_campaign_assets_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `campaign_id` (BIGINT UNSIGNED, FK→campaigns, cascadeOnDelete)
  - `asset_type` (ENUM: 'image', 'video', 'document', 'other')
  - `file_path` (VARCHAR 500)
  - `mime_type` (VARCHAR 120, nullable)
  - `file_size` (BIGINT UNSIGNED, nullable)
  - `title` (VARCHAR 255, nullable)
  - `sort_order` (INT, DEFAULT 0)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 29. **campaign_applications**
- **Creation Migration:** `2026_03_29_000023_create_campaign_applications_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `campaign_id` (BIGINT UNSIGNED, FK→campaigns, cascadeOnDelete)
  - `influencer_id` (BIGINT UNSIGNED, FK→influencers, cascadeOnDelete) **[Named `creator_id` in creation, renamed to `influencer_id` in #72]**
  - `status` (ENUM: 'invited', 'applied', 'shortlisted', 'approved', 'rejected', 'completed', DEFAULT 'applied')
  - `pitch_message` (TEXT, nullable)
  - `proposed_rate` (DECIMAL 12,2, nullable)
  - `agreed_rate` (DECIMAL 12,2, nullable)
  - `applied_at` (TIMESTAMP, nullable)
  - `decided_at` (TIMESTAMP, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Unique Constraint:** `['campaign_id', 'influencer_id']` (named: `campaign_creator_unique`)
- **Incremental Migrations:**
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (column rename)

---

### 30. **carts**
- **Creation Migration:** `2026_03_29_000024_create_carts_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `user_id` (BIGINT UNSIGNED, FK→users, cascadeOnDelete)
  - `status` (ENUM: 'active', 'converted', 'abandoned', DEFAULT 'active')
  - `expires_at` (TIMESTAMP, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 31. **packages**
- **Creation Migration:** `2026_03_29_000025_create_packages_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `influencer_id` (BIGINT UNSIGNED, FK→influencers, cascadeOnDelete) **[Named `creator_id` in creation, renamed in #72]**
  - `created_by` (BIGINT UNSIGNED, nullable, FK→users, nullOnDelete)
  - `platform` (ENUM: 'facebook', 'instagram', 'tiktok', 'linkedin', 'x', 'youtube', 'ugc', 'other')
  - `name` (VARCHAR 255)
  - `description` (TEXT, nullable)
  - `base_price` (DECIMAL 12,2)
  - `currency` (CHAR 3, DEFAULT 'USD')
  - `delivery_days` (SMALLINT UNSIGNED, nullable)
  - `revisions_included` (SMALLINT UNSIGNED, nullable)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:**
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (column rename)

---

### 32. **cart_items**
- **Creation Migration:** `2026_03_29_000026_create_cart_items_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `cart_id` (BIGINT UNSIGNED, FK→carts, cascadeOnDelete)
  - `package_id` (BIGINT UNSIGNED, FK→packages, cascadeOnDelete)
  - `influencer_id` (BIGINT UNSIGNED, FK→influencers, cascadeOnDelete) **[Renamed in #72]**
  - `campaign_id` (BIGINT UNSIGNED, nullable, FK→campaigns, nullOnDelete)
  - `quantity` (INT UNSIGNED, DEFAULT 1)
  - `unit_price` (DECIMAL 12,2)
  - `currency` (CHAR 3, DEFAULT 'USD')
  - `notes` (TEXT, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:**
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (column rename)

---

### 33. **orders**
- **Creation Migration:** `2026_03_29_000027_create_orders_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `order_number` (VARCHAR 50, UNIQUE)
  - `buyer_user_id` (BIGINT UNSIGNED, FK→users, cascadeOnDelete)
  - `brand_id` (BIGINT UNSIGNED, nullable, FK→brands, nullOnDelete)
  - `campaign_id` (BIGINT UNSIGNED, nullable, FK→campaigns, nullOnDelete)
  - `status` (ENUM: 'pending', 'accepted', 'in_progress', 'delivered', 'completed', 'cancelled', 'refunded', DEFAULT 'pending')
  - `accepted_by_user_id` (BIGINT UNSIGNED, nullable, FK→users, nullOnDelete)
  - `accepted_for_influencer_id` (BIGINT UNSIGNED, nullable, FK→influencers, nullOnDelete) **[Renamed in #72]**
  - `subtotal` (DECIMAL 12,2, DEFAULT 0)
  - `service_fee` (DECIMAL 12,2, DEFAULT 0)
  - `tax_amount` (DECIMAL 12,2, DEFAULT 0)
  - `total_amount` (DECIMAL 12,2, DEFAULT 0)
  - `currency` (CHAR 3, DEFAULT 'USD')
  - `placed_at` (TIMESTAMP, nullable)
  - `accepted_at` (TIMESTAMP, nullable)
  - `completed_at` (TIMESTAMP, nullable)
  - `cancelled_at` (TIMESTAMP, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:**
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (column rename)

---

### 34. **order_items**
- **Creation Migration:** `2026_03_29_000028_create_order_items_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `order_id` (BIGINT UNSIGNED, FK→orders, cascadeOnDelete)
  - `influencer_id` (BIGINT UNSIGNED, FK→influencers, cascadeOnDelete) **[Renamed in #72]**
  - `package_id` (BIGINT UNSIGNED, nullable, FK→packages, nullOnDelete)
  - `campaign_id` (BIGINT UNSIGNED, nullable, FK→campaigns, nullOnDelete)
  - `title` (VARCHAR 255)
  - `description` (TEXT, nullable)
  - `quantity` (INT UNSIGNED, DEFAULT 1)
  - `unit_price` (DECIMAL 12,2)
  - `line_total` (DECIMAL 12,2)
  - `status` (ENUM: 'pending', 'accepted', 'in_progress', 'delivered', 'approved', 'rejected', 'cancelled', DEFAULT 'pending')
  - `due_date` (DATE, nullable)
  - `accepted_by_user_id` (BIGINT UNSIGNED, nullable, FK→users, nullOnDelete)
  - `accepted_at` (TIMESTAMP, nullable)
  - `delivered_at` (TIMESTAMP, nullable)
  - `approved_at` (TIMESTAMP, nullable)
  - `paid_at` (TIMESTAMP, nullable) **[Added in Migration #68]**
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:**
  - #68: `2026_04_05_062950_add_paid_at_to_order_items_table.php` (ADD column)
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (column rename)

---

### 35. **order_deliverables**
- **Creation Migration:** `2026_03_29_000029_create_order_deliverables_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `order_item_id` (BIGINT UNSIGNED, FK→order_items, cascadeOnDelete)
  - `uploaded_by_user_id` (BIGINT UNSIGNED, FK→users, cascadeOnDelete)
  - `deliverable_type` (ENUM: 'image', 'video', 'document', 'link', 'other')
  - `file_path` (VARCHAR 500, nullable)
  - `external_url` (VARCHAR 500, nullable)
  - `notes` (TEXT, nullable)
  - `status` (ENUM: 'submitted', 'approved', 'changes_requested', 'rejected', DEFAULT 'submitted')
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 36. **order_messages**
- **Creation Migration:** `2026_03_29_000030_create_order_messages_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `order_id` (BIGINT UNSIGNED, FK→orders, cascadeOnDelete)
  - `sender_user_id` (BIGINT UNSIGNED, FK→users, cascadeOnDelete)
  - `message` (TEXT)
  - `is_system` (BOOLEAN, DEFAULT false)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 37. **order_status_history**
- **Creation Migration:** `2026_03_29_000031_create_order_status_history_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `order_id` (BIGINT UNSIGNED, FK→orders, cascadeOnDelete)
  - `old_status` (VARCHAR 50, nullable)
  - `new_status` (VARCHAR 50)
  - `changed_by_user_id` (BIGINT UNSIGNED, nullable, FK→users, nullOnDelete)
  - `note` (TEXT, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 38. **conversations**
- **Creation Migration:** `2026_03_29_000032_create_conversations_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `public_id` (VARCHAR 32, UNIQUE, nullable) **[Added in Migration #69, backfilled in #70]**
  - `conversation_type` (ENUM: 'influencer_profile', 'order', DEFAULT 'influencer_profile')
  - `influencer_id` (BIGINT UNSIGNED, FK→influencers, cascadeOnDelete) **[Renamed in #72]**
  - `brand_user_id` (BIGINT UNSIGNED, FK→users, cascadeOnDelete)
  - `handled_by_user_id` (BIGINT UNSIGNED, nullable, FK→users, nullOnDelete)
  - `order_id` (BIGINT UNSIGNED, nullable, FK→orders, nullOnDelete)
  - `influencer_direct_message_enabled` (BOOLEAN, DEFAULT false)
  - `title` (VARCHAR 255, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Indexes (added in #71):**
    - `['brand_user_id', 'updated_at']` (named: `conversations_brand_user_id_updated_at_index`)
    - `['influencer_id']` (named: `conversations_influencer_id_index`)
    - `['handled_by_user_id']` (named: `conversations_handled_by_user_id_index`)
    - `['order_id']` (named: `conversations_order_id_index`)
    - `['public_id']` (named: `conversations_public_id_index`)
    - `['brand_user_id', 'influencer_id']` (named: `conversations_brand_creator_index`)
- **Incremental Migrations:**
  - #69: `2026_04_06_000001_add_public_id_to_conversations_table.php` (ADD column)
  - #70: `2026_04_06_000002_backfill_public_id_conversations.php` (DATA migration)
  - #71: `2026_04_06_000003_optimize_conversations_and_messages_indexes.php` (ADD indexes)
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (column rename)

---

### 39. **conversation_participants**
- **Creation Migration:** `2026_03_29_000033_create_conversation_participants_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `conversation_id` (BIGINT UNSIGNED, FK→conversations, cascadeOnDelete)
  - `user_id` (BIGINT UNSIGNED, FK→users, cascadeOnDelete)
  - `participant_role` (ENUM: 'brand', 'moderator', 'admin')
  - `joined_at` (TIMESTAMP, nullable)
  - `left_at` (TIMESTAMP, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Unique Constraint:** `['conversation_id', 'user_id']`
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 40. **messages**
- **Creation Migration:** `2026_03_29_000034_create_messages_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `conversation_id` (BIGINT UNSIGNED, FK→conversations, cascadeOnDelete)
  - `sender_user_id` (BIGINT UNSIGNED, FK→users, cascadeOnDelete)
  - `sender_role` (ENUM: 'brand', 'moderator', 'admin', 'system')
  - `on_behalf_of_influencer_id` (BIGINT UNSIGNED, nullable, FK→influencers, nullOnDelete) **[Renamed in #72]**
  - `message` (LONGTEXT)
  - `attachment_path` (VARCHAR 500, nullable)
  - `read_at` (TIMESTAMP, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Indexes (added in #71):**
    - `['conversation_id', 'created_at']` (named: `messages_conversation_id_created_at_index`)
    - `['conversation_id', 'read_at']` (named: `messages_conversation_id_read_at_index`)
    - `['sender_user_id']` (named: `messages_sender_user_id_index`)
    - `['created_at']` (named: `messages_created_at_index`)
- **Incremental Migrations:**
  - #71: `2026_04_06_000003_optimize_conversations_and_messages_indexes.php` (ADD indexes)
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (column rename)

---

### 41. **payments**
- **Creation Migration:** `2026_03_29_000034_create_payments_table.php` (⚠️ Duplicate timestamp with messages)
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `order_id` (BIGINT UNSIGNED, FK→orders, cascadeOnDelete)
  - `payment_provider` (VARCHAR 80)
  - `provider_payment_id` (VARCHAR 120, nullable)
  - `payment_method_id` (BIGINT UNSIGNED, nullable, FK→payment_methods, nullOnDelete) **[Added in Migration #66]**
  - `amount` (DECIMAL 12,2)
  - `currency` (CHAR 3, DEFAULT 'USD')
  - `status` (ENUM: 'pending', 'authorized', 'captured', 'failed', 'refunded', 'partially_refunded', DEFAULT 'pending')
  - `paid_at` (TIMESTAMP, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:**
  - #66: `2026_03_30_000122_add_payment_method_id_to_payments_table.php` (ADD column)

---

### 42. **payout_accounts**
- **Creation Migration:** `2026_03_29_000035_create_payout_accounts_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `influencer_id` (BIGINT UNSIGNED, FK→influencers, cascadeOnDelete) **[Renamed in #72]**
  - `provider` (VARCHAR 80)
  - `account_identifier` (VARCHAR 255)
  - `account_name` (VARCHAR 255, nullable)
  - `is_default` (BOOLEAN, DEFAULT false)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:**
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (column rename)

---

### 43. **payouts**
- **Creation Migration:** `2026_03_29_000036_create_payouts_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `influencer_id` (BIGINT UNSIGNED, FK→influencers, cascadeOnDelete) **[Renamed in #72]**
  - `payout_account_id` (BIGINT UNSIGNED, FK→payout_accounts, cascadeOnDelete)
  - `amount` (DECIMAL 12,2)
  - `currency` (CHAR 3, DEFAULT 'USD')
  - `status` (ENUM: 'pending', 'processing', 'paid', 'failed', 'cancelled', DEFAULT 'pending')
  - `external_payout_id` (VARCHAR 120, nullable)
  - `paid_at` (TIMESTAMP, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:**
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (column rename)

---

### 44. **payout_items**
- **Creation Migration:** `2026_03_29_000037_create_payout_items_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `payout_id` (BIGINT UNSIGNED, FK→payouts, cascadeOnDelete)
  - `order_item_id` (BIGINT UNSIGNED, FK→order_items, cascadeOnDelete)
  - `amount` (DECIMAL 12,2)
  - `currency` (CHAR 3, DEFAULT 'USD')
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Unique Constraint:** `['payout_id', 'order_item_id']`
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 45. **reviews**
- **Creation Migration:** `2026_03_29_000038_create_reviews_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `order_item_id` (BIGINT UNSIGNED, UNIQUE, FK→order_items, cascadeOnDelete)
  - `brand_id` (BIGINT UNSIGNED, FK→brands, cascadeOnDelete)
  - `influencer_id` (BIGINT UNSIGNED, FK→influencers, cascadeOnDelete) **[Renamed in #72]**
  - `rating` (TINYINT UNSIGNED)
  - `title` (VARCHAR 255, nullable)
  - `comment` (TEXT, nullable)
  - `is_public` (BOOLEAN, DEFAULT true)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:**
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (column rename)

---

### 46. **wishlists**
- **Creation Migration:** `2026_03_29_000039_create_wishlists_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `user_id` (BIGINT UNSIGNED, FK→users, cascadeOnDelete)
  - `name` (VARCHAR 150)
  - `is_default` (BOOLEAN, DEFAULT false)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 47. **wishlist_items**
- **Creation Migration:** `2026_03_29_000040_create_wishlist_items_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `wishlist_id` (BIGINT UNSIGNED, FK→wishlists, cascadeOnDelete)
  - `influencer_id` (BIGINT UNSIGNED, FK→influencers, cascadeOnDelete) **[Renamed in #72]**
  - `notes` (VARCHAR 255, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Unique Constraint:** `['wishlist_id', 'influencer_id']`
- **Incremental Migrations:**
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (column rename)

---

### 48. **pages**
- **Creation Migration:** `2026_03_29_000041_create_pages_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `slug` (VARCHAR 120, UNIQUE)
  - `title` (VARCHAR 255)
  - `meta_title` (VARCHAR 255, nullable)
  - `meta_description` (VARCHAR 500, nullable)
  - `is_published` (BOOLEAN, DEFAULT true)
  - `published_at` (TIMESTAMP, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 49. **page_sections**
- **Creation Migration:** `2026_03_29_000042_create_page_sections_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `page_id` (BIGINT UNSIGNED, FK→pages, cascadeOnDelete)
  - `section_key` (VARCHAR 120)
  - `heading` (VARCHAR 255, nullable)
  - `subheading` (VARCHAR 500, nullable)
  - `content_json` (JSON, nullable)
  - `sort_order` (INT, DEFAULT 0)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 50. **case_studies**
- **Creation Migration:** `2026_03_29_000043_create_case_studies_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `title` (VARCHAR 255)
  - `slug` (VARCHAR 255, UNIQUE)
  - `summary` (TEXT, nullable)
  - `cover_image_path` (VARCHAR 500, nullable)
  - `external_url` (VARCHAR 500, nullable)
  - `is_published` (BOOLEAN, DEFAULT true)
  - `sort_order` (INT, DEFAULT 0)
  - `published_at` (TIMESTAMP, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 51. **testimonials**
- **Creation Migration:** `2026_03_29_000044_create_testimonials_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `author_name` (VARCHAR 255)
  - `author_role` (VARCHAR 255, nullable)
  - `company_name` (VARCHAR 255, nullable)
  - `quote` (TEXT)
  - `rating` (TINYINT UNSIGNED, nullable)
  - `is_published` (BOOLEAN, DEFAULT true)
  - `sort_order` (INT, DEFAULT 0)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 52. **featured_collaborations**
- **Creation Migration:** `2026_03_29_000045_create_featured_collaborations_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `brand_name` (VARCHAR 255, nullable)
  - `asset_type` (ENUM: 'image', 'video')
  - `image_path` (VARCHAR 500, nullable)
  - `video_path` (VARCHAR 500, nullable)
  - `thumbnail_path` (VARCHAR 500, nullable)
  - `sort_order` (INT, DEFAULT 0)
  - `is_published` (BOOLEAN, DEFAULT true)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 53. **faq_sections**
- **Creation Migration:** `2026_03_29_000046_create_faq_sections_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `section_code` (VARCHAR 120, UNIQUE)
  - `section_title` (VARCHAR 255)
  - `audience_type` (ENUM: 'all', 'brand', 'influencer', DEFAULT 'all') **[Updated in Migration #74]**
  - `sort_order` (INT, DEFAULT 0)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:**
  - #74: `2026_04_07_000003_update_faq_sections_audience_type_to_influencer.php` (UPDATE enum values)

---

### 54. **faq_items**
- **Creation Migration:** `2026_03_29_000047_create_faq_items_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `faq_section_id` (BIGINT UNSIGNED, FK→faq_sections, cascadeOnDelete)
  - `question` (VARCHAR 500)
  - `answer` (LONGTEXT)
  - `sort_order` (INT, DEFAULT 0)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 55. **support_categories**
- **Creation Migration:** `2026_03_29_000048_create_support_categories_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `name` (VARCHAR 150)
  - `slug` (VARCHAR 180, UNIQUE)
  - `description` (VARCHAR 500, nullable)
  - `sort_order` (INT, DEFAULT 0)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 56. **support_articles**
- **Creation Migration:** `2026_03_29_000049_create_support_articles_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `support_category_id` (BIGINT UNSIGNED, FK→support_categories, cascadeOnDelete)
  - `title` (VARCHAR 255)
  - `slug` (VARCHAR 255, UNIQUE)
  - `short_description` (VARCHAR 500, nullable)
  - `body` (LONGTEXT)
  - `is_published` (BOOLEAN, DEFAULT true)
  - `published_at` (TIMESTAMP, nullable)
  - `sort_order` (INT, DEFAULT 0)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 57. **support_questions**
- **Creation Migration:** `2026_03_29_000050_create_support_questions_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `support_category_id` (BIGINT UNSIGNED, FK→support_categories, cascadeOnDelete)
  - `question` (VARCHAR 500)
  - `answer` (LONGTEXT)
  - `sort_order` (INT, DEFAULT 0)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 58. **support_tickets**
- **Creation Migration:** `2026_03_29_000051_create_support_tickets_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `ticket_number` (VARCHAR 60, UNIQUE)
  - `requester_user_id` (BIGINT UNSIGNED, FK→users, cascadeOnDelete)
  - `support_category_id` (BIGINT UNSIGNED, FK→support_categories, cascadeOnDelete)
  - `assigned_to_user_id` (BIGINT UNSIGNED, nullable, FK→users, nullOnDelete)
  - `subject` (VARCHAR 255)
  - `description` (LONGTEXT)
  - `priority` (ENUM: 'low', 'medium', 'high', 'urgent', DEFAULT 'medium')
  - `status` (ENUM: 'open', 'in_progress', 'waiting_user', 'resolved', 'closed', DEFAULT 'open')
  - `source` (ENUM: 'web', 'email', 'admin', DEFAULT 'web')
  - `resolved_at` (TIMESTAMP, nullable)
  - `closed_at` (TIMESTAMP, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 59. **support_ticket_messages**
- **Creation Migration:** `2026_03_29_000052_create_support_ticket_messages_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `support_ticket_id` (BIGINT UNSIGNED, FK→support_tickets, cascadeOnDelete)
  - `sender_user_id` (BIGINT UNSIGNED, FK→users, cascadeOnDelete)
  - `message` (LONGTEXT)
  - `is_internal_note` (BOOLEAN, DEFAULT false)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 60. **support_ticket_attachments**
- **Creation Migration:** `2026_03_29_000053_create_support_ticket_attachments_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `support_ticket_message_id` (BIGINT UNSIGNED, FK→support_ticket_messages, cascadeOnDelete)
  - `uploaded_by_user_id` (BIGINT UNSIGNED, FK→users, cascadeOnDelete)
  - `file_path` (VARCHAR 500)
  - `file_name` (VARCHAR 255, nullable)
  - `mime_type` (VARCHAR 120, nullable)
  - `file_size` (BIGINT UNSIGNED, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 61. **notifications**
- **Creation Migration:** `2026_03_29_000054_create_notifications_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `user_id` (BIGINT UNSIGNED, FK→users, cascadeOnDelete)
  - `type` (VARCHAR 120)
  - `title` (VARCHAR 255)
  - `body` (TEXT, nullable)
  - `data_json` (JSON, nullable)
  - `is_read` (BOOLEAN, DEFAULT false)
  - `read_at` (TIMESTAMP, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 62. **media_library**
- **Creation Migration:** `2026_03_29_000055_create_media_library_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `uploaded_by_user_id` (BIGINT UNSIGNED, FK→users, cascadeOnDelete)
  - `disk` (VARCHAR 60, DEFAULT 'public')
  - `path` (VARCHAR 500)
  - `file_name` (VARCHAR 255, nullable)
  - `mime_type` (VARCHAR 120, nullable)
  - `file_size` (BIGINT UNSIGNED, nullable)
  - `width` (INT, nullable)
  - `height` (INT, nullable)
  - `alt_text` (VARCHAR 255, nullable)
  - `entity_type` (VARCHAR 120, nullable)
  - `entity_id` (BIGINT UNSIGNED, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 63-67. **RBAC Tables** (Created in single migration)
- **Creation Migration:** `2026_03_29_000056_create_rbac_tables.php`

#### 63. **roles**
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `name` (VARCHAR 150)
  - `slug` (VARCHAR 180, UNIQUE)
  - `description` (TEXT, nullable)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None

#### 64. **permissions**
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `name` (VARCHAR 150)
  - `slug` (VARCHAR 180, UNIQUE)
  - `module` (VARCHAR 120)
  - `description` (TEXT, nullable)
  - `is_active` (BOOLEAN, DEFAULT true)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Incremental Migrations:** None

#### 65. **user_roles**
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `user_id` (BIGINT UNSIGNED, FK→users, cascadeOnDelete)
  - `role_id` (BIGINT UNSIGNED, FK→roles, cascadeOnDelete)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Unique Constraint:** `['user_id', 'role_id']` (named: `uq_user_roles_user_role`)
- **Incremental Migrations:** None

#### 66. **role_permissions**
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `role_id` (BIGINT UNSIGNED, FK→roles, cascadeOnDelete)
  - `permission_id` (BIGINT UNSIGNED, FK→permissions, cascadeOnDelete)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Unique Constraint:** `['role_id', 'permission_id']` (named: `uq_role_permissions_role_permission`)
- **Incremental Migrations:** None

#### 67. **user_permissions**
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `user_id` (BIGINT UNSIGNED, FK→users, cascadeOnDelete)
  - `permission_id` (BIGINT UNSIGNED, FK→permissions, cascadeOnDelete)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Unique Constraint:** `['user_id', 'permission_id']` (named: `uq_user_permissions_user_permission`)
- **Incremental Migrations:** None

---

### 68. **campaign_influencers**
- **Creation Migration:** `2026_03_29_000057_create_campaign_influencers_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `campaign_id` (BIGINT UNSIGNED, FK→campaigns, cascadeOnDelete)
  - `influencer_id` (BIGINT UNSIGNED, FK→influencers, cascadeOnDelete) **[Renamed in #72]**
  - `status` (ENUM: 'assigned', 'approved', 'rejected', 'cancelled', DEFAULT 'assigned')
  - `approved_by` (BIGINT UNSIGNED, nullable, FK→users, nullOnDelete)
  - `approved_at` (TIMESTAMP, nullable)
  - `cancelled_at` (TIMESTAMP, nullable)
  - `rejection_reason` (TEXT, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Unique Constraint:** `['campaign_id', 'influencer_id']`
- **Incremental Migrations:**
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (column rename)

---

### 69. **sub_orders**
- **Creation Migration:** `2026_03_29_000058_create_sub_orders_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `order_id` (BIGINT UNSIGNED, FK→orders, cascadeOnDelete)
  - `campaign_influencer_id` (BIGINT UNSIGNED, FK→campaign_influencers, cascadeOnDelete)
  - `influencer_id` (BIGINT UNSIGNED, FK→influencers, cascadeOnDelete) **[Renamed in #72]**
  - `status` (ENUM: 'pending', 'accepted', 'in_progress', 'on_review', 'completed', 'cancelled', DEFAULT 'pending')
  - `deliverables` (LONGTEXT, nullable)
  - `amount` (DECIMAL 12,2)
  - `currency` (CHAR 3, DEFAULT 'USD')
  - `accepted_at` (TIMESTAMP, nullable)
  - `completed_at` (TIMESTAMP, nullable)
  - `cancelled_at` (TIMESTAMP, nullable)
  - `paid_at` (TIMESTAMP, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Indexes:**
    - `['order_id']`
    - `['influencer_id']`
- **Incremental Migrations:**
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (column rename)

---

### 70. **moderator_assignments**
- **Creation Migration:** `2026_03_29_000059_create_moderator_assignments_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `moderator_user_id` (BIGINT UNSIGNED, FK→users, cascadeOnDelete)
  - `influencer_id` (BIGINT UNSIGNED, FK→influencers, cascadeOnDelete) **[Renamed in #72]**
  - `assigned_at` (TIMESTAMP)
  - `unassigned_at` (TIMESTAMP, nullable)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Indexes:**
    - `['moderator_user_id']`
    - `['influencer_id']`
    - **Unique:** `['influencer_id', 'unassigned_at']`
- **Incremental Migrations:**
  - #72: `2026_04_07_000001_rename_creators_to_influencers.php` (column rename)

---

### 71. **knowledge_base_articles**
- **Creation Migration:** `2026_03_30_000120_create_knowledge_base_articles_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `title` (VARCHAR 255)
  - `slug` (VARCHAR 255, UNIQUE)
  - `badge` (VARCHAR 120, nullable)
  - `summary` (VARCHAR 500, nullable)
  - `content` (LONGTEXT)
  - `read_time_minutes` (TINYINT UNSIGNED, DEFAULT 5)
  - `is_featured` (BOOLEAN, DEFAULT false)
  - `is_published` (BOOLEAN, DEFAULT true)
  - `published_at` (TIMESTAMP, nullable)
  - `sort_order` (INT, DEFAULT 0)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Indexes:**
    - `['is_published', 'sort_order']`
    - `['is_featured', 'is_published']`
- **Incremental Migrations:** None
- **Status:** No alterations

---

### 72. **payment_methods**
- **Creation Migration:** `2026_03_30_000121_create_payment_methods_table.php`
- **Final Columns:**
  - `id` (BIGINT, Primary Key, Auto-increment)
  - `user_id` (BIGINT UNSIGNED, FK→users, cascadeOnDelete)
  - `provider` (ENUM: 'stripe', 'manual', DEFAULT 'stripe')
  - `provider_payment_method_id` (VARCHAR 255, nullable)
  - `last4` (VARCHAR 4)
  - `brand` (VARCHAR 50, nullable)
  - `expiry_month` (TINYINT UNSIGNED, nullable)
  - `expiry_year` (SMALLINT UNSIGNED, nullable)
  - `is_default` (BOOLEAN, DEFAULT false, Indexed)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
  - **Indexes:**
    - `['user_id']`
    - `['user_id', 'is_default']`
- **Incremental Migrations:** None
- **Status:** No alterations

---

## 5. CONFLICTING SCHEMA DEFINITIONS

### Issues Identified:

#### Issue #1: Mismatched user_type Enum in `billing_profiles`
- **Table:** `billing_profiles`
- **Problem:** The `user_type` enum still contains the old value 'creator' instead of 'influencer'
- **Location:** Migration #5: `2026_03_29_000005_create_billing_profiles_table.php`
- **NOT updated by:** Migration #73 which only updates `users` table
- **Risk:** Potential inconsistency if the enum is used for validation
- **Recommendation:** Add a new migration to update `billing_profiles.user_type` enum to match the updated user types

#### Issue #2: Duplicate Timestamp Execution Order
- **Timestamps:** `2026_03_29_000034`
- **Files:**
  - `2026_03_29_000034_create_messages_table.php`
  - `2026_03_29_000034_create_payments_table.php`
- **Problem:** Laravel will execute these in filesystem order, but the order is not guaranteed
- **Dependency Chain:** 
  - `payments` depends on `orders` (FK)
  - `messages` depends on `conversations` (FK)
  - These are independent, BUT the duplicate timestamp is a violation of migration naming convention
- **Recommendation:** Rename one to `2026_03_29_000034a` or increment to `2026_03_29_000035`

#### Issue #3: Duplicate Timestamp Execution Order
- **Timestamps:** `2026_03_30_000122`
- **Files:**
  - `2026_03_30_000122_add_payment_method_id_to_payments_table.php`
  - `2026_03_30_000122_add_stripe_customer_id_to_users_table.php`
- **Problem:** Duplicate timestamp creates execution order ambiguity
- **Both files:** Modify different tables (independent operations)
- **Recommendation:** Rename one to `2026_03_30_000122a` or increment to `2026_03_30_000123`

---

## 6. MIGRATION EXECUTION SUMMARY

### Statistics:
- **Total migrations:** 74
- **CREATE table migrations:** 65
- **ALTER table migrations:** 9 (incremental)
- **Data migration migrations:** 1 (backfill)
- **RENAME migrations:** 1 (complex multi-operation)
- **ENUM update migrations:** 2 (data type changes)

### Duplicate Timestamp Issues: 2 (CRITICAL)
### Conflicting Schema Issues: 1 (schema consistency)
### Tables Created: 72
### Incremental Modifications: 9

---

## RECOMMENDATIONS

### Immediate Actions (CRITICAL):

1. **Fix Duplicate Timestamp Migrations:**
   - Rename `2026_03_29_000034_create_payments_table.php` → `2026_03_29_000034a_create_payments_table.php` 
   - Rename `2026_03_30_000122_add_stripe_customer_id_to_users_table.php` → `2026_03_30_000122a_add_stripe_customer_id_to_users_table.php`

2. **Update `billing_profiles.user_type` Enum:**
   ```php
   // New migration: 2026_04_07_000004_update_billing_profiles_user_type_enum.php
   Schema::table('billing_profiles', function (Blueprint $table) {
       DB::statement("ALTER TABLE `billing_profiles` MODIFY `user_type` ENUM('brand', 'influencer') DEFAULT 'brand'");
   });
   DB::table('billing_profiles')->where('user_type', 'creator')->update(['user_type' => 'influencer']);
   ```

3. **Verify Role-Based Access Control:**
   - Ensure RBAC tables (`roles`, `permissions`, `user_roles`, `role_permissions`, `user_permissions`) are properly seeded

### Best Practices for Future Migrations:

1. Always increment migration timestamps sequentially (never duplicate)
2. Keep one operation per migration file for clarity
3. Test migration rollbacks to ensure `down()` methods work correctly
4. Verify foreign key constraints after major renames
5. Document any data migrations with clear comments

---

**Report Generated:** April 7, 2026  
**Total Analysis Time:** Comprehensive  
**All 74 Migration Files Analyzed:** ✓
