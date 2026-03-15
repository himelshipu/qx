# Complete Database Plan

## 1. Tables and Columns

### users

- id BIGINT UNSIGNED PK AI
- name VARCHAR(255) NOT NULL
- email VARCHAR(255) NOT NULL UNIQUE
- user_type ENUM('brand','creator','moderator','admin') NOT NULL DEFAULT 'brand'
- verification_code VARCHAR(20) NULL
- verification_code_expires_at TIMESTAMP NULL
- email_verified_at TIMESTAMP NULL
- password VARCHAR(255) NOT NULL
- remember_token VARCHAR(100) NULL
- phone VARCHAR(30) NULL
- date_of_birth DATE NULL
- gender ENUM('male','female','other') NULL
- country VARCHAR(120) NULL
- city VARCHAR(120) NULL
- postal_code VARCHAR(30) NULL
- company_name VARCHAR(255) NULL
- job_title VARCHAR(255) NULL
- bio TEXT NULL
- profile_image_path VARCHAR(500) NULL
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- last_login_at TIMESTAMP NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### roles

- id BIGINT UNSIGNED PK AI
- name VARCHAR(150) NOT NULL
- slug VARCHAR(180) NOT NULL UNIQUE
- description TEXT NULL
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### permissions

- id BIGINT UNSIGNED PK AI
- name VARCHAR(150) NOT NULL
- slug VARCHAR(180) NOT NULL UNIQUE
- module VARCHAR(120) NOT NULL
- description TEXT NULL
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### user_roles

- id BIGINT UNSIGNED PK AI
- user_id BIGINT UNSIGNED NOT NULL
- role_id BIGINT UNSIGNED NOT NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL
- UNIQUE KEY uq_user_roles_user_role (user_id, role_id)

### role_permissions

- id BIGINT UNSIGNED PK AI
- role_id BIGINT UNSIGNED NOT NULL
- permission_id BIGINT UNSIGNED NOT NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL
- UNIQUE KEY uq_role_permissions_role_permission (role_id, permission_id)

### user_permissions

- id BIGINT UNSIGNED PK AI
- user_id BIGINT UNSIGNED NOT NULL
- permission_id BIGINT UNSIGNED NOT NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL
- UNIQUE KEY uq_user_permissions_user_permission (user_id, permission_id)

### brands

- id BIGINT UNSIGNED PK AI
- user_id BIGINT UNSIGNED NOT NULL UNIQUE
- brand_name VARCHAR(255) NOT NULL
- description TEXT NULL
- industry VARCHAR(150) NULL
- phone VARCHAR(30) NULL
- email VARCHAR(255) NULL
- website VARCHAR(500) NULL
- location VARCHAR(255) NULL
- city VARCHAR(120) NULL
- country VARCHAR(120) NULL
- postal_code VARCHAR(30) NULL
- profile_image_path VARCHAR(500) NULL
- cover_image_path VARCHAR(500) NULL
- is_verified BOOLEAN NOT NULL DEFAULT FALSE
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### brand_social_links

- id BIGINT UNSIGNED PK AI
- brand_id BIGINT UNSIGNED NOT NULL UNIQUE
- instagram_url VARCHAR(500) NULL
- tiktok_url VARCHAR(500) NULL
- facebook_url VARCHAR(500) NULL
- x_url VARCHAR(500) NULL
- youtube_url VARCHAR(500) NULL
- other_url VARCHAR(500) NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### brand_billing_profiles

- id BIGINT UNSIGNED PK AI
- brand_id BIGINT UNSIGNED NOT NULL UNIQUE
- legal_company_name VARCHAR(255) NULL
- vat_id VARCHAR(120) NULL
- billing_address VARCHAR(255) NULL
- billing_city VARCHAR(120) NULL
- billing_country VARCHAR(120) NULL
- billing_postal_code VARCHAR(30) NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### brand_onboarding_profiles

- id BIGINT UNSIGNED PK AI
- brand_id BIGINT UNSIGNED NOT NULL UNIQUE
- objective VARCHAR(120) NULL
- budget_range VARCHAR(120) NULL
- business_type VARCHAR(120) NULL
- company_size VARCHAR(120) NULL
- is_completed BOOLEAN NOT NULL DEFAULT FALSE
- completed_at TIMESTAMP NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### categories

- id BIGINT UNSIGNED PK AI
- name VARCHAR(150) NOT NULL
- slug VARCHAR(180) NOT NULL UNIQUE
- description TEXT NULL
- icon_path VARCHAR(500) NULL
- image_path VARCHAR(500) NULL
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- sort_order INT NOT NULL DEFAULT 0
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### brand_onboarding_industries

- id BIGINT UNSIGNED PK AI
- brand_onboarding_profile_id BIGINT UNSIGNED NOT NULL
- category_id BIGINT UNSIGNED NOT NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL
- UNIQUE KEY uq_onboarding_industry (brand_onboarding_profile_id, category_id)

### creators

- id BIGINT UNSIGNED PK AI
- user_id BIGINT UNSIGNED NOT NULL UNIQUE
- display_name VARCHAR(255) NULL
- title_name VARCHAR(255) NULL
- description TEXT NULL
- audience TEXT NULL
- brands_worked_with TEXT NULL
- location VARCHAR(255) NULL
- city VARCHAR(120) NULL
- country VARCHAR(120) NULL
- postal_code VARCHAR(30) NULL
- gender ENUM('male','female','other') NULL
- profile_image_path VARCHAR(500) NULL
- cover_image_path VARCHAR(500) NULL
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### creator_social_links

- id BIGINT UNSIGNED PK AI
- creator_id BIGINT UNSIGNED NOT NULL UNIQUE
- facebook_url VARCHAR(500) NULL
- youtube_url VARCHAR(500) NULL
- tiktok_url VARCHAR(500) NULL
- linkedin_url VARCHAR(500) NULL
- x_url VARCHAR(500) NULL
- other_url VARCHAR(500) NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### creator_platform_stats

- id BIGINT UNSIGNED PK AI
- creator_id BIGINT UNSIGNED NOT NULL
- platform ENUM('instagram','tiktok','youtube','linkedin','facebook','x','twitch','ugc','other') NOT NULL
- handle VARCHAR(255) NULL
- profile_url VARCHAR(500) NULL
- follower_count BIGINT UNSIGNED NULL
- avg_views BIGINT UNSIGNED NULL
- engagement_rate DECIMAL(5,2) NULL
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL
- UNIQUE KEY uq_creator_platform (creator_id, platform)

### creator_categories

- id BIGINT UNSIGNED PK AI
- creator_id BIGINT UNSIGNED NOT NULL
- category_id BIGINT UNSIGNED NOT NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL
- UNIQUE KEY uq_creator_category (creator_id, category_id)

### badge_definitions

- id BIGINT UNSIGNED PK AI
- code VARCHAR(120) NOT NULL UNIQUE
- name VARCHAR(120) NOT NULL
- description TEXT NULL
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### creator_badges

- id BIGINT UNSIGNED PK AI
- creator_id BIGINT UNSIGNED NOT NULL
- badge_definition_id BIGINT UNSIGNED NOT NULL
- earned_at TIMESTAMP NULL
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL
- UNIQUE KEY uq_creator_badge (creator_id, badge_definition_id)

### campaigns

- id BIGINT UNSIGNED PK AI
- title VARCHAR(255) NOT NULL
- campaign_type ENUM('instagram','tiktok','ugc','youtube','twitch','other') NOT NULL
- description TEXT NULL
- instructions LONGTEXT NULL
- status ENUM('draft','published','paused','closed','archived') NOT NULL DEFAULT 'draft'
- budget_min DECIMAL(12,2) NULL
- budget_max DECIMAL(12,2) NULL
- currency CHAR(3) NOT NULL DEFAULT 'USD'
- start_date DATE NULL
- end_date DATE NULL
- published_at TIMESTAMP NULL
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### campaign_targeting

- id BIGINT UNSIGNED PK AI
- campaign_id BIGINT UNSIGNED NOT NULL UNIQUE
- influencer_count INT UNSIGNED NULL
- target_gender ENUM('any','male','female','other') NOT NULL DEFAULT 'any'
- age_min TINYINT UNSIGNED NULL
- age_max TINYINT UNSIGNED NULL
- notes TEXT NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### campaign_categories

- id BIGINT UNSIGNED PK AI
- campaign_id BIGINT UNSIGNED NOT NULL
- category_id BIGINT UNSIGNED NOT NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL
- UNIQUE KEY uq_campaign_category (campaign_id, category_id)

### campaign_target_countries

- id BIGINT UNSIGNED PK AI
- campaign_id BIGINT UNSIGNED NOT NULL
- country_code CHAR(2) NOT NULL
- country_name VARCHAR(120) NOT NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL
- UNIQUE KEY uq_campaign_country (campaign_id, country_code)

### follower_ranges

- id BIGINT UNSIGNED PK AI
- code VARCHAR(80) NOT NULL UNIQUE
- label VARCHAR(120) NOT NULL
- min_followers BIGINT UNSIGNED NULL
- max_followers BIGINT UNSIGNED NULL
- sort_order INT NOT NULL DEFAULT 0
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### campaign_target_follower_ranges

- id BIGINT UNSIGNED PK AI
- campaign_id BIGINT UNSIGNED NOT NULL
- follower_range_id BIGINT UNSIGNED NOT NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL
- UNIQUE KEY uq_campaign_follower_range (campaign_id, follower_range_id)

### campaign_assets

- id BIGINT UNSIGNED PK AI
- campaign_id BIGINT UNSIGNED NOT NULL
- asset_type ENUM('image','video','document','other') NOT NULL
- file_path VARCHAR(500) NOT NULL
- mime_type VARCHAR(120) NULL
- file_size BIGINT UNSIGNED NULL
- title VARCHAR(255) NULL
- sort_order INT NOT NULL DEFAULT 0
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### campaign_applications

- id BIGINT UNSIGNED PK AI
- campaign_id BIGINT UNSIGNED NOT NULL
- creator_id BIGINT UNSIGNED NOT NULL
- status ENUM('invited','applied','shortlisted','approved','rejected','completed') NOT NULL DEFAULT 'applied'
- pitch_message TEXT NULL
- proposed_rate DECIMAL(12,2) NULL
- agreed_rate DECIMAL(12,2) NULL
- applied_at TIMESTAMP NULL
- decided_at TIMESTAMP NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL
- UNIQUE KEY uq_campaign_creator_application (campaign_id, creator_id)

### packages

- id BIGINT UNSIGNED PK AI
- platform ENUM('instagram','tiktok','youtube','ugc','other') NOT NULL
- name VARCHAR(255) NOT NULL
- description TEXT NULL
- base_price DECIMAL(12,2) NOT NULL
- currency CHAR(3) NOT NULL DEFAULT 'USD'
- delivery_days SMALLINT UNSIGNED NULL
- revisions_included SMALLINT UNSIGNED NULL
- created_by_user_id BIGINT UNSIGNED NOT NULL
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### carts

- id BIGINT UNSIGNED PK AI
- user_id BIGINT UNSIGNED NOT NULL
- status ENUM('active','converted','abandoned') NOT NULL DEFAULT 'active'
- expires_at TIMESTAMP NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### cart_items

- id BIGINT UNSIGNED PK AI
- cart_id BIGINT UNSIGNED NOT NULL
- package_id BIGINT UNSIGNED NOT NULL
- creator_id BIGINT UNSIGNED NOT NULL
- campaign_id BIGINT UNSIGNED NULL
- quantity INT UNSIGNED NOT NULL DEFAULT 1
- unit_price DECIMAL(12,2) NOT NULL
- currency CHAR(3) NOT NULL DEFAULT 'USD'
- notes TEXT NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### orders

- id BIGINT UNSIGNED PK AI
- order_number VARCHAR(50) NOT NULL UNIQUE
- buyer_user_id BIGINT UNSIGNED NOT NULL
- brand_id BIGINT UNSIGNED NULL
- campaign_id BIGINT UNSIGNED NULL
- status ENUM('pending','accepted','in_progress','delivered','completed','cancelled','refunded') NOT NULL DEFAULT 'pending'
- accepted_by_user_id BIGINT UNSIGNED NULL
- accepted_for_creator_id BIGINT UNSIGNED NULL
- subtotal DECIMAL(12,2) NOT NULL DEFAULT 0.00
- service_fee DECIMAL(12,2) NOT NULL DEFAULT 0.00
- tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00
- total_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00
- currency CHAR(3) NOT NULL DEFAULT 'USD'
- placed_at TIMESTAMP NULL
- accepted_at TIMESTAMP NULL
- completed_at TIMESTAMP NULL
- cancelled_at TIMESTAMP NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### order_items

- id BIGINT UNSIGNED PK AI
- order_id BIGINT UNSIGNED NOT NULL
- creator_id BIGINT UNSIGNED NOT NULL
- package_id BIGINT UNSIGNED NULL
- campaign_id BIGINT UNSIGNED NULL
- title VARCHAR(255) NOT NULL
- description TEXT NULL
- quantity INT UNSIGNED NOT NULL DEFAULT 1
- unit_price DECIMAL(12,2) NOT NULL
- line_total DECIMAL(12,2) NOT NULL
- status ENUM('pending','accepted','in_progress','delivered','approved','rejected','cancelled') NOT NULL DEFAULT 'pending'
- due_date DATE NULL
- accepted_by_user_id BIGINT UNSIGNED NULL
- accepted_at TIMESTAMP NULL
- delivered_at TIMESTAMP NULL
- approved_at TIMESTAMP NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### order_status_history

- id BIGINT UNSIGNED PK AI
- order_id BIGINT UNSIGNED NOT NULL
- old_status VARCHAR(50) NULL
- new_status VARCHAR(50) NOT NULL
- changed_by_user_id BIGINT UNSIGNED NULL
- note TEXT NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### order_messages

- id BIGINT UNSIGNED PK AI
- order_id BIGINT UNSIGNED NOT NULL
- sender_user_id BIGINT UNSIGNED NOT NULL
- message TEXT NOT NULL
- is_system BOOLEAN NOT NULL DEFAULT FALSE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### order_deliverables

- id BIGINT UNSIGNED PK AI
- order_item_id BIGINT UNSIGNED NOT NULL
- uploaded_by_user_id BIGINT UNSIGNED NOT NULL
- deliverable_type ENUM('image','video','document','link','other') NOT NULL
- file_path VARCHAR(500) NULL
- external_url VARCHAR(500) NULL
- notes TEXT NULL
- status ENUM('submitted','approved','changes_requested','rejected') NOT NULL DEFAULT 'submitted'
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### payments

- id BIGINT UNSIGNED PK AI
- order_id BIGINT UNSIGNED NOT NULL
- payment_provider VARCHAR(80) NOT NULL
- provider_payment_id VARCHAR(120) NULL
- amount DECIMAL(12,2) NOT NULL
- currency CHAR(3) NOT NULL DEFAULT 'USD'
- status ENUM('pending','authorized','captured','failed','refunded','partially_refunded') NOT NULL DEFAULT 'pending'
- paid_at TIMESTAMP NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### payout_accounts

- id BIGINT UNSIGNED PK AI
- creator_id BIGINT UNSIGNED NOT NULL
- provider VARCHAR(80) NOT NULL
- account_identifier VARCHAR(255) NOT NULL
- account_name VARCHAR(255) NULL
- is_default BOOLEAN NOT NULL DEFAULT FALSE
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### payouts

- id BIGINT UNSIGNED PK AI
- creator_id BIGINT UNSIGNED NOT NULL
- payout_account_id BIGINT UNSIGNED NOT NULL
- amount DECIMAL(12,2) NOT NULL
- currency CHAR(3) NOT NULL DEFAULT 'USD'
- status ENUM('pending','processing','paid','failed','cancelled') NOT NULL DEFAULT 'pending'
- external_payout_id VARCHAR(120) NULL
- paid_at TIMESTAMP NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### payout_items

- id BIGINT UNSIGNED PK AI
- payout_id BIGINT UNSIGNED NOT NULL
- order_item_id BIGINT UNSIGNED NOT NULL
- amount DECIMAL(12,2) NOT NULL
- currency CHAR(3) NOT NULL DEFAULT 'USD'
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL
- UNIQUE KEY uq_payout_order_item (payout_id, order_item_id)

### reviews

- id BIGINT UNSIGNED PK AI
- order_item_id BIGINT UNSIGNED NOT NULL UNIQUE
- brand_id BIGINT UNSIGNED NOT NULL
- creator_id BIGINT UNSIGNED NOT NULL
- rating TINYINT UNSIGNED NOT NULL
- title VARCHAR(255) NULL
- comment TEXT NULL
- is_public BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### wishlists

- id BIGINT UNSIGNED PK AI
- user_id BIGINT UNSIGNED NOT NULL
- name VARCHAR(150) NOT NULL
- is_default BOOLEAN NOT NULL DEFAULT FALSE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### wishlist_items

- id BIGINT UNSIGNED PK AI
- wishlist_id BIGINT UNSIGNED NOT NULL
- creator_id BIGINT UNSIGNED NOT NULL
- notes VARCHAR(255) NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL
- UNIQUE KEY uq_wishlist_creator (wishlist_id, creator_id)

### pages

- id BIGINT UNSIGNED PK AI
- slug VARCHAR(120) NOT NULL UNIQUE
- title VARCHAR(255) NOT NULL
- meta_title VARCHAR(255) NULL
- meta_description VARCHAR(500) NULL
- is_published BOOLEAN NOT NULL DEFAULT TRUE
- published_at TIMESTAMP NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### page_sections

- id BIGINT UNSIGNED PK AI
- page_id BIGINT UNSIGNED NOT NULL
- section_key VARCHAR(120) NOT NULL
- heading VARCHAR(255) NULL
- subheading VARCHAR(500) NULL
- content_json JSON NULL
- sort_order INT NOT NULL DEFAULT 0
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### case_studies

- id BIGINT UNSIGNED PK AI
- page_id BIGINT UNSIGNED NULL
- title VARCHAR(255) NOT NULL
- slug VARCHAR(255) NOT NULL UNIQUE
- summary TEXT NULL
- cover_image_path VARCHAR(500) NULL
- external_url VARCHAR(500) NULL
- is_published BOOLEAN NOT NULL DEFAULT TRUE
- sort_order INT NOT NULL DEFAULT 0
- published_at TIMESTAMP NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### testimonials

- id BIGINT UNSIGNED PK AI
- page_id BIGINT UNSIGNED NULL
- author_name VARCHAR(255) NOT NULL
- author_role VARCHAR(255) NULL
- company_name VARCHAR(255) NULL
- quote TEXT NOT NULL
- rating TINYINT UNSIGNED NULL
- is_published BOOLEAN NOT NULL DEFAULT TRUE
- sort_order INT NOT NULL DEFAULT 0
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### featured_collaborations

- id BIGINT UNSIGNED PK AI
- page_id BIGINT UNSIGNED NULL
- brand_name VARCHAR(255) NULL
- asset_type ENUM('image','video') NOT NULL
- image_path VARCHAR(500) NULL
- video_path VARCHAR(500) NULL
- thumbnail_path VARCHAR(500) NULL
- sort_order INT NOT NULL DEFAULT 0
- is_published BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### faq_sections

- id BIGINT UNSIGNED PK AI
- page_id BIGINT UNSIGNED NULL
- section_code VARCHAR(120) NOT NULL UNIQUE
- section_title VARCHAR(255) NOT NULL
- audience_type ENUM('all','brand','creator') NOT NULL DEFAULT 'all'
- sort_order INT NOT NULL DEFAULT 0
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### faq_items

- id BIGINT UNSIGNED PK AI
- faq_section_id BIGINT UNSIGNED NOT NULL
- question VARCHAR(500) NOT NULL
- answer LONGTEXT NOT NULL
- sort_order INT NOT NULL DEFAULT 0
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### support_categories

- id BIGINT UNSIGNED PK AI
- name VARCHAR(150) NOT NULL
- slug VARCHAR(180) NOT NULL UNIQUE
- description VARCHAR(500) NULL
- sort_order INT NOT NULL DEFAULT 0
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### support_articles

- id BIGINT UNSIGNED PK AI
- support_category_id BIGINT UNSIGNED NOT NULL
- title VARCHAR(255) NOT NULL
- slug VARCHAR(255) NOT NULL UNIQUE
- short_description VARCHAR(500) NULL
- body LONGTEXT NOT NULL
- is_published BOOLEAN NOT NULL DEFAULT TRUE
- published_at TIMESTAMP NULL
- sort_order INT NOT NULL DEFAULT 0
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### support_questions

- id BIGINT UNSIGNED PK AI
- support_category_id BIGINT UNSIGNED NOT NULL
- question VARCHAR(500) NOT NULL
- answer LONGTEXT NOT NULL
- sort_order INT NOT NULL DEFAULT 0
- is_active BOOLEAN NOT NULL DEFAULT TRUE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### support_tickets

- id BIGINT UNSIGNED PK AI
- ticket_number VARCHAR(60) NOT NULL UNIQUE
- requester_user_id BIGINT UNSIGNED NOT NULL
- support_category_id BIGINT UNSIGNED NOT NULL
- assigned_to_user_id BIGINT UNSIGNED NULL
- subject VARCHAR(255) NOT NULL
- description LONGTEXT NOT NULL
- priority ENUM('low','medium','high','urgent') NOT NULL DEFAULT 'medium'
- status ENUM('open','in_progress','waiting_user','resolved','closed') NOT NULL DEFAULT 'open'
- source ENUM('web','email','admin') NOT NULL DEFAULT 'web'
- resolved_at TIMESTAMP NULL
- closed_at TIMESTAMP NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### support_ticket_messages

- id BIGINT UNSIGNED PK AI
- support_ticket_id BIGINT UNSIGNED NOT NULL
- sender_user_id BIGINT UNSIGNED NOT NULL
- message LONGTEXT NOT NULL
- is_internal_note BOOLEAN NOT NULL DEFAULT FALSE
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### support_ticket_attachments

- id BIGINT UNSIGNED PK AI
- support_ticket_message_id BIGINT UNSIGNED NOT NULL
- uploaded_by_user_id BIGINT UNSIGNED NOT NULL
- file_path VARCHAR(500) NOT NULL
- file_name VARCHAR(255) NULL
- mime_type VARCHAR(120) NULL
- file_size BIGINT UNSIGNED NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### conversations

- id BIGINT UNSIGNED PK AI
- conversation_type ENUM('creator_profile','order') NOT NULL DEFAULT 'creator_profile'
- creator_id BIGINT UNSIGNED NOT NULL
- brand_user_id BIGINT UNSIGNED NOT NULL
- handled_by_user_id BIGINT UNSIGNED NULL
- order_id BIGINT UNSIGNED NULL
- creator_direct_message_enabled BOOLEAN NOT NULL DEFAULT FALSE
- title VARCHAR(255) NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### conversation_participants

- id BIGINT UNSIGNED PK AI
- conversation_id BIGINT UNSIGNED NOT NULL
- user_id BIGINT UNSIGNED NOT NULL
- participant_role ENUM('brand','moderator','admin') NOT NULL
- joined_at TIMESTAMP NULL
- left_at TIMESTAMP NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL
- UNIQUE KEY uq_conversation_user (conversation_id, user_id)

### messages

- id BIGINT UNSIGNED PK AI
- conversation_id BIGINT UNSIGNED NOT NULL
- sender_user_id BIGINT UNSIGNED NOT NULL
- sender_role ENUM('brand','moderator','admin','system') NOT NULL
- on_behalf_of_creator_id BIGINT UNSIGNED NULL
- message LONGTEXT NOT NULL
- attachment_path VARCHAR(500) NULL
- read_at TIMESTAMP NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### notifications

- id BIGINT UNSIGNED PK AI
- user_id BIGINT UNSIGNED NOT NULL
- type VARCHAR(120) NOT NULL
- title VARCHAR(255) NOT NULL
- body TEXT NULL
- data_json JSON NULL
- is_read BOOLEAN NOT NULL DEFAULT FALSE
- read_at TIMESTAMP NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### media_library

- id BIGINT UNSIGNED PK AI
- uploaded_by_user_id BIGINT UNSIGNED NOT NULL
- disk VARCHAR(60) NOT NULL DEFAULT 'public'
- path VARCHAR(500) NOT NULL
- file_name VARCHAR(255) NULL
- mime_type VARCHAR(120) NULL
- file_size BIGINT UNSIGNED NULL
- width INT NULL
- height INT NULL
- alt_text VARCHAR(255) NULL
- entity_type VARCHAR(120) NULL
- entity_id BIGINT UNSIGNED NULL
- created_at TIMESTAMP NULL
- updated_at TIMESTAMP NULL

### password_reset_tokens

- email VARCHAR(255) PK
- token VARCHAR(255) NOT NULL
- created_at TIMESTAMP NULL

### sessions

- id VARCHAR(255) PK
- user_id BIGINT UNSIGNED NULL
- ip_address VARCHAR(45) NULL
- user_agent TEXT NULL
- payload LONGTEXT NOT NULL
- last_activity INT NOT NULL

### cache

- key VARCHAR(255) PK
- value MEDIUMTEXT NOT NULL
- expiration INT NOT NULL

### cache_locks

- key VARCHAR(255) PK
- owner VARCHAR(255) NOT NULL
- expiration INT NOT NULL

### jobs

- id BIGINT UNSIGNED PK AI
- queue VARCHAR(255) NOT NULL
- payload LONGTEXT NOT NULL
- attempts TINYINT UNSIGNED NOT NULL
- reserved_at INT UNSIGNED NULL
- available_at INT UNSIGNED NOT NULL
- created_at INT UNSIGNED NOT NULL

### job_batches

- id VARCHAR(255) PK
- name VARCHAR(255) NOT NULL
- total_jobs INT NOT NULL
- pending_jobs INT NOT NULL
- failed_jobs INT NOT NULL
- failed_job_ids LONGTEXT NOT NULL
- options MEDIUMTEXT NULL
- cancelled_at INT NULL
- created_at INT NOT NULL
- finished_at INT NULL

### failed_jobs

- id BIGINT UNSIGNED PK AI
- uuid VARCHAR(255) NOT NULL UNIQUE
- connection TEXT NOT NULL
- queue TEXT NOT NULL
- payload LONGTEXT NOT NULL
- exception LONGTEXT NOT NULL
- failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP

## 2. Relations

- users.id -> user_roles.user_id (1:N)
- roles.id -> user_roles.role_id (1:N)
- roles.id -> role_permissions.role_id (1:N)
- permissions.id -> role_permissions.permission_id (1:N)
- users.id -> user_permissions.user_id (1:N)
- permissions.id -> user_permissions.permission_id (1:N)

- users.id -> brands.user_id (1:1)
- brands.id -> brand_social_links.brand_id (1:1)
- brands.id -> brand_billing_profiles.brand_id (1:1)
- brands.id -> brand_onboarding_profiles.brand_id (1:1)
- brand_onboarding_profiles.id -> brand_onboarding_industries.brand_onboarding_profile_id (1:N)
- categories.id -> brand_onboarding_industries.category_id (1:N)

- users.id -> creators.user_id (1:1)
- creators.id -> creator_social_links.creator_id (1:1)
- creators.id -> creator_platform_stats.creator_id (1:N)
- creators.id -> creator_categories.creator_id (1:N)
- categories.id -> creator_categories.category_id (1:N)
- creators.id -> creator_badges.creator_id (1:N)
- badge_definitions.id -> creator_badges.badge_definition_id (1:N)

- campaigns.id -> campaign_targeting.campaign_id (1:1)
- campaigns.id -> campaign_categories.campaign_id (1:N)
- categories.id -> campaign_categories.category_id (1:N)
- campaigns.id -> campaign_target_countries.campaign_id (1:N)
- campaigns.id -> campaign_target_follower_ranges.campaign_id (1:N)
- follower_ranges.id -> campaign_target_follower_ranges.follower_range_id (1:N)
- campaigns.id -> campaign_assets.campaign_id (1:N)
- campaigns.id -> campaign_applications.campaign_id (1:N)
- creators.id -> campaign_applications.creator_id (1:N)

- users.id -> packages.created_by_user_id (1:N)
- users.id -> carts.user_id (1:N)
- carts.id -> cart_items.cart_id (1:N)
- packages.id -> cart_items.package_id (1:N)
- creators.id -> cart_items.creator_id (1:N)
- campaigns.id -> cart_items.campaign_id (1:N, nullable)

- users.id -> orders.buyer_user_id (1:N)
- brands.id -> orders.brand_id (1:N, nullable)
- campaigns.id -> orders.campaign_id (1:N, nullable)
- users.id -> orders.accepted_by_user_id (1:N, nullable)
- creators.id -> orders.accepted_for_creator_id (1:N, nullable)
- orders.id -> order_items.order_id (1:N)
- creators.id -> order_items.creator_id (1:N)
- packages.id -> order_items.package_id (1:N, nullable)
- campaigns.id -> order_items.campaign_id (1:N, nullable)
- users.id -> order_items.accepted_by_user_id (1:N, nullable)
- orders.id -> order_status_history.order_id (1:N)
- users.id -> order_status_history.changed_by_user_id (1:N, nullable)
- orders.id -> order_messages.order_id (1:N)
- users.id -> order_messages.sender_user_id (1:N)
- order_items.id -> order_deliverables.order_item_id (1:N)
- users.id -> order_deliverables.uploaded_by_user_id (1:N)
- orders.id -> payments.order_id (1:N)

- creators.id -> payout_accounts.creator_id (1:N)
- creators.id -> payouts.creator_id (1:N)
- payout_accounts.id -> payouts.payout_account_id (1:N)
- payouts.id -> payout_items.payout_id (1:N)
- order_items.id -> payout_items.order_item_id (1:N)

- order_items.id -> reviews.order_item_id (1:1)
- brands.id -> reviews.brand_id (1:N)
- creators.id -> reviews.creator_id (1:N)

- users.id -> wishlists.user_id (1:N)
- wishlists.id -> wishlist_items.wishlist_id (1:N)
- creators.id -> wishlist_items.creator_id (1:N)

- pages.id -> page_sections.page_id (1:N)
- pages.id -> case_studies.page_id (1:N, nullable)
- pages.id -> testimonials.page_id (1:N, nullable)
- pages.id -> featured_collaborations.page_id (1:N, nullable)
- pages.id -> faq_sections.page_id (1:N, nullable)
- faq_sections.id -> faq_items.faq_section_id (1:N)

- support_categories.id -> support_articles.support_category_id (1:N)
- support_categories.id -> support_questions.support_category_id (1:N)
- support_categories.id -> support_tickets.support_category_id (1:N)
- users.id -> support_tickets.requester_user_id (1:N)
- users.id -> support_tickets.assigned_to_user_id (1:N, nullable)
- support_tickets.id -> support_ticket_messages.support_ticket_id (1:N)
- users.id -> support_ticket_messages.sender_user_id (1:N)
- support_ticket_messages.id -> support_ticket_attachments.support_ticket_message_id (1:N)
- users.id -> support_ticket_attachments.uploaded_by_user_id (1:N)

- creators.id -> conversations.creator_id (1:N)
- users.id -> conversations.brand_user_id (1:N)
- users.id -> conversations.handled_by_user_id (1:N, nullable)
- conversations.id -> conversation_participants.conversation_id (1:N)
- users.id -> conversation_participants.user_id (1:N)
- conversations.id -> messages.conversation_id (1:N)
- users.id -> messages.sender_user_id (1:N)
- creators.id -> messages.on_behalf_of_creator_id (1:N, nullable)
- orders.id -> conversations.order_id (1:N, nullable)

- users.id -> notifications.user_id (1:N)
- users.id -> media_library.uploaded_by_user_id (1:N)
- users.id -> sessions.user_id (1:N, nullable)
