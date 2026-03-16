# Project Structure Documentation

## Overview

This document provides a comprehensive overview of the project folder and file structure based on the database schema. The project is a Laravel-based influencer marketing platform with separate backend (admin) and frontend components.

---

## 1. Project Root Structure

```
/var/www/qx/
├── app/                          # Main application code
├── bootstrap/                    # Application bootstrapping
├── config/                       # Configuration files
├── database/                     # Migrations, seeders, factories
├── public/                       # Publicly accessible files
├── resources/                    # Views, assets, JS/CSS
├── routes/                       # Route definitions
├── storage/                     # Logs, cache, uploads
├── tests/                        # Test files
├── vendor/                       # Composer dependencies
├── artisan                       # Laravel CLI
├── composer.json                 # PHP dependencies
├── package.json                  # Node.js dependencies
├── vite.config.js                # Vite configuration
├── tailwind.config.js            # Tailwind CSS configuration
├── DATABASE_STRUCTURE.md         # Database schema documentation
└── IMPLEMENTATION_SUMMARY.md    # Implementation notes
```

---

## 2. Backend Structure (app/Http/)

```
app/Http/
├── Controllers/
│   ├── Controller.php                    # Base controller
│   ├── Auth/
│   │   ├── AuthenticatedSessionController.php
│   │   ├── ConfirmablePasswordController.php
│   │   ├── NewPasswordController.php
│   │   ├── PasswordController.php
│   │   ├── PasswordResetLinkController.php
│   │   ├── RegisteredUserController.php
│   │   └── VerificationCodeController.php
│   ├── Backend/
│   │   ├── BrandController.php           # Brand CRUD operations
│   │   ├── CampaignController.php        # Campaign CRUD operations
│   │   ├── CategoryController.php        # Category CRUD operations
│   │   ├── CreatorController.php         # Creator CRUD operations
│   │   ├── DashboardController.php       # Dashboard statistics
│   │   ├── ModeratorController.php       # Moderator management
│   │   ├── PermissionController.php      # Permission management
│   │   └── RoleController.php            # Role management
│   └── Frontend/
│       ├── ContentLibraryController.php
│       ├── HomeController.php
│       ├── InfluencersController.php
│       └── StaticPagesController.php
│
├── Middleware/
│   ├── Authenticate.php                  # User authentication
│   ├── EncryptCookies.php                # Cookie encryption
│   ├── PreventRequestsDuringMaintenance.php
│   ├── RedirectIfAuthenticated.php
│   ├── TrimStrings.php
│   ├── TrustHosts.php
│   ├── TrustProxies.php
│   └── VerifyCsrfToken.php
│
├── Requests/
│   ├── Auth/
│   │   └── LoginRequest.php
│   └── Backend/
│       ├── Brand/
│       │   ├── StoreBrandRequest.php     # Brand creation validation
│       │   └── UpdateBrandRequest.php     # Brand update validation
│       ├── Campaign/
│       │   ├── StoreCampaignRequest.php  # Campaign creation validation
│       │   └── UpdateCampaignRequest.php  # Campaign update validation
│       ├── Category/
│       │   ├── StoreCategoryRequest.php
│       │   └── UpdateCategoryRequest.php
│       ├── Creator/
│       │   ├── StoreCreatorRequest.php
│       │   └── UpdateCreatorRequest.php
│       └── Moderator/
│           ├── StoreModeratorRequest.php
│           └── UpdateModeratorRequest.php
│
└── Resources/
    └── UserResource.php                   # API Resource for User
```

---

## 3. Models (app/Models/)

```
app/Models/
├── User.php                               # User model (multi-type: brand, creator, moderator, admin)
├── Role.php                               # Role model
├── Permission.php                         # Permission model
├── Brand.php                              # Brand profile model
├── BrandSocialLink.php                    # Brand social links
├── BrandBillingProfile.php                # Brand billing information
├── BrandOnboardingProfile.php             # Brand onboarding data
├── Category.php                           # Category model
├── Creator.php                            # Creator profile model
├── CreatorSocialLink.php                  # Creator social links
├── CreatorPlatformStat.php                # Creator platform statistics
├── CreatorBadge.php                       # Creator earned badges
├── BadgeDefinition.php                    # Badge definitions
├── Campaign.php                           # Campaign model
├── CampaignTargeting.php                  # Campaign targeting settings
├── CampaignAsset.php                      # Campaign assets (images, videos, docs)
├── CampaignApplication.php                # Campaign applications
├── CampaignCategory.php                   # Campaign-Category relationship
├── CampaignTargetCountry.php              # Campaign target countries
├── FollowerRange.php                      # Follower range definitions
├── Package.php                            # Creator service packages
├── Cart.php                               # Shopping cart
├── CartItem.php                           # Cart items
├── Order.php                              # Order model
├── OrderItem.php                          # Order items
├── OrderStatusHistory.php                 # Order status change history
├── OrderMessage.php                       # Order messages
├── OrderDeliverable.php                   # Order deliverables/files
├── Payment.php                            # Payment records
├── PayoutAccount.php                      # Creator payout accounts
├── Payout.php                             # Payout records
└── PayoutItem.php                         # Payout line items
```

---

## 4. Services (app/Services/)

```
app/Services/
├── Admin/
│   ├── AdminDashboardService.php          # Dashboard statistics
│   ├── BrandService.php                   # Brand business logic
│   ├── CampaignService.php                # Campaign business logic
│   ├── CategoryService.php                # Category business logic
│   ├── CreatorService.php                 # Creator business logic
│   └── ModeratorService.php               # Moderator management logic
│
└── Web/
    └── HomeService.php                    # Frontend home page data
```

---

## 5. Repositories (app/Repositories/)

```
app/Repositories/
├── Contracts/
│   ├── BrandRepositoryInterface.php
│   ├── CampaignRepositoryInterface.php
│   ├── CategoryRepositoryInterface.php
│   ├── CreatorRepositoryInterface.php
│   ├── ModeratorRepositoryInterface.php
│   └── UserRepositoryInterface.php
│
└── Eloquent/
    ├── EloquentBrandRepository.php
    ├── EloquentCampaignRepository.php
    ├── EloquentCategoryRepository.php
    ├── EloquentCreatorRepository.php
    ├── EloquentModeratorRepository.php
    └── EloquentUserRepository.php
```

---

## 6. DTOs (Data Transfer Objects)

```
app/DTOs/
└── HomeDataDTO.php                        # Homepage data transfer object
```

---

## 7. View Components (app/View/Components/)

```
app/View/Components/
├── backend/
│   ├── Shell/
│   │   ├── Backdrop.php
│   │   ├── Breadcrumb.php
│   │   ├── CalenderArea.php
│   │   ├── Chart.php
│   │   ├── Header.php
│   │   ├── Percentage.php
│   │   ├── Sidebar.php
│   │   ├── StatisticsChart.php
│   │   └── ...
│   ├── dropdowns/
│   │   ├── Menu.php
│   │   ├── Notification.php
│   │   └── User.php
│   └── form/
│       ├── CheckboxComponent.php
│       ├── DatePicker.php
│       ├── DefaultInputs.php
│       ├── Dropzone.php
│       ├── FileInputExample.php
│       ├── InputGroup.php
│       ├── InputStates.php
│       ├── MultipleSelect.php
│       ├── Radio.php
│       ├── RadioButtons.php
│       ├── SelectInputs.php
│       ├── TextAreaInputs.php
│       └── ToggleSwitch.php
│
├── frontend/
│   ├── navigation/
│   │   ├── AuthHeader.php
│   │   ├── Footer.php
│   │   ├── Header.php
│   │   └── Hero.php
│   └── partials/
│       ├── Campaign.php
│       ├── Cases.php
│       ├── Cta.php
│       ├── Faq.php
│       ├── Featured.php
│       └── Search.php
│
├── profile/
│   ├── AddressCard.php
│   ├── PersonalInfoCard.php
│   └── ProfileCard.php
│
└── ui/
    ├── Alert.php
    ├── Avatar.php
    ├── Badge.php
    ├── Button.php
    ├── Modal.php
    └── YoutubeEmbed.php
```

---

## 8. Database Structure (database/)

```
database/
├── migrations/                            # Database migrations
│   ├── 2024_01_01_000001_create_users_table.php
│   ├── 2024_01_01_000002_create_roles_table.php
│   ├── 2024_01_01_000003_create_permissions_table.php
│   ├── 2024_01_01_000004_create_brands_table.php
│   ├── 2024_01_01_000005_create_creators_table.php
│   ├── 2024_01_01_000006_create_categories_table.php
│   ├── 2024_01_01_000007_create_campaigns_table.php
│   ├── 2024_01_01_000008_create_orders_table.php
│   ├── 2024_01_01_000009_create_payments_table.php
│   ├── 2024_01_01_000010_create_payouts_table.php
│   └── ... (more migrations)
│
├── seeders/                               # Database seeders
│   ├── DatabaseSeeder.php
│   ├── RoleSeeder.php
│   ├── PermissionSeeder.php
│   ├── CategorySeeder.php
│   ├── FollowerRangeSeeder.php
│   └── BadgeDefinitionSeeder.php
│
└── factories/                             # Model factories
    ├── UserFactory.php
    ├── BrandFactory.php
    ├── CreatorFactory.php
    ├── CampaignFactory.php
    ├── OrderFactory.php
    └── ...
```

---

## 9. Routes (routes/)

```
routes/
├── web.php                                # Web routes
├── api.php                                # API routes (if needed)
├── console.php                            # Console commands
└── channels.php                           # Broadcasting channels
```

### API Endpoints Structure

| Entity | CRUD Operations | Endpoints |
|--------|----------------|-----------|
| Users | Create, Read, Update, Delete | `/api/users` |
| Brands | Create, Read, Update, Delete | `/api/brands`, `/api/brands/{id}/social-links` |
| Creators | Create, Read, Update, Delete | `/api/creators`, `/api/creators/{id}/stats` |
| Campaigns | Create, Read, Update, Delete | `/api/campaigns`, `/api/campaigns/{id}/applications` |
| Categories | Create, Read, Update, Delete | `/api/categories` |
| Orders | Create, Read, Update | `/api/orders`, `/api/orders/{id}/items` |
| Payments | Create, Read | `/api/payments` |
| Payouts | Create, Read | `/api/payouts` |

---

## 10. Frontend Resources (resources/)

```
resources/
├── css/
│   ├── app.css                           # Main Tailwind CSS
│   └── custom.css                        # Custom styles
│
├── js/
│   ├── app.js                            # Main JavaScript entry
│   └── components/
│       └── admin/
│           ├── chart/
│           │   ├── chart-1.js
│           │   ├── chart-2.js
│           │   ├── chart-3.js
│           │   ├── chart-6.js
│           │   ├── chart-8.js
│           │   └── chart-13.js
│           └── calendar-init.js
│
├── views/
│   ├── auth/                              # Authentication views
│   │   ├── login.blade.php
│   │   ├── register.blade.php
│   │   ├── forgot-password.blade.php
│   │   ├── verify-email.blade.php
│   │   └── ...
│   │
│   ├── backend/                          # Admin dashboard views
│   │   ├── layouts/
│   │   │   └── app.blade.php            # Main admin layout
│   │   │
│   │   └── pages/
│   │       ├── campaigns/
│   │       │   ├── index.blade.php
│   │       │   ├── create.blade.php
│   │       │   ├── edit.blade.php
│   │       │   ├── view.blade.php
│   │       │   ├── designed-index.blade.php
│   │       │   └── designed-create.blade.php
│   │       │
│   │       ├── brands/
│   │       ├── creators/
│   │       ├── categories/
│   │       ├── roles/
│   │       ├── permissions/
│   │       └── moderators/
│   │       └── ...
│   │
│   ├── frontend/                         # Public-facing views
│   │   ├── layouts/
│   │   ├── pages/
│   │   │   ├── home.blade.php
│   │   │   ├── account.blade.php
│   │   │   ├── brand-edit-profile.blade.php
│   │   │   └── ...
│   │   └── components/
│   │
│   ├── components/                       # Blade components
│   │   └── form/
│   │       └── date-picker.blade.php
│   │
│   └── emails/                           # Email templates
│
└── sass/                                 # SCSS files (if used)
```

---

## 11. Configuration (config/)

```
config/
├── app.php                # Application configuration
├── auth.php               # Authentication guards
├── cache.php              # Cache configuration
├── database.php           # Database connections
├── filesystems.php        # Filesystem configuration
├── logging.php            # Logging configuration
├── mail.php               # Mail configuration
├── queue.php              # Queue configuration
├── session.php            # Session configuration
└── view.php              # View configuration
```

---

## 12. Helpers (app/Helpers/)

```
app/Helpers/
└── MenuHelper.php         # Menu generation helper
```

---

## 13. Event Listeners (app/Listeners/)

```
app/Listeners/
└── SendEmailVerificationNotification.php
```

---

## 14. Mail (app/Mail/)

```
app/Mail/
└── SendVerificationCodeMail.php
```

---

## 15. Service Providers (app/Providers/)

```
app/Providers/
├── AppServiceProvider.php         # Application service provider
├── EventServiceProvider.php       # Event service provider
└── RepositoryServiceProvider.php  # Repository bindings
```

---

## 16. Entity-Controller Mapping

| Database Table | Model | Controller | Service | Repository |
|---------------|-------|------------|---------|------------|
| users | User | - | - | EloquentUserRepository |
| roles | Role | RoleController | - | - |
| permissions | Permission | PermissionController | - | - |
| brands | Brand | BrandController | BrandService | - |
| creators | Creator | CreatorController | CreatorService | - |
| campaigns | Campaign | CampaignController | CampaignService | - |
| categories | Category | CategoryController | CategoryService | - |
| orders | Order | - | - | - |
| payments | Payment | - | - | - |
| payouts | Payout | - | - | - |
| packages | Package | - | - | - |
| carts | Cart | - | - | - |

---

## 17. Naming Conventions

### Models
- Singular, PascalCase (e.g., `Campaign`, `User`)
- File: `app/Models/{ModelName}.php`

### Controllers
- Plural, PascalCase with "Controller" suffix (e.g., `CampaignController`)
- File: `app/Http/Controllers/{Type}/{Name}Controller.php`

### Services
- Singular, PascalCase with "Service" suffix (e.g., `CampaignService`)
- File: `app/Services/{Type}/{Name}Service.php`

### Requests (Form Requests)
- Singular, PascalCase with "Request" suffix (e.g., `StoreCampaignRequest`)
- File: `app/Http/Requests/{Type}/{Action}{Name}Request.php`

### Views
- kebab-case (e.g., `campaign-index.blade.php`)
- Folder: `resources/views/{type}/{entity}/`

### Migrations
- Timestamp prefix: `2024_01_01_000001_create_{table}_table.php`

---

## 18. API Request/Response Patterns

### Store Campaign Request Example
```php
// Request: POST /api/campaigns
{
    "title": "Summer Campaign 2024",
    "campaign_type": "instagram",
    "description": "Product launch campaign",
    "status": "draft",
    "budget_min": 1000.00,
    "budget_max": 5000.00,
    "currency": "USD",
    "start_date": "2024-06-01",
    "end_date": "2024-08-31",
    "categories": [1, 2, 3],
    "target_countries": ["US", "UK", "CA"],
    "target_gender": "any",
    "age_min": 18,
    "age_max": 45
}
```

### Campaign Response Example
```json
{
    "id": 1,
    "title": "Summer Campaign 2024",
    "campaign_type": "instagram",
    "status": "draft",
    "budget_min": "1000.00",
    "budget_max": "5000.00",
    "currency": "USD",
    "start_date": "2024-06-01",
    "end_date": "2024-08-31",
    "created_at": "2024-01-15T10:30:00Z",
    "updated_at": "2024-01-15T10:30:00Z"
}
```

---

## 19. Best Practices

1. **Repository Pattern**: Use repositories for data access abstraction
2. **Service Layer**: Keep business logic in services
3. **Form Requests**: Use Laravel Form Requests for validation
4. **API Resources**: Use API Resources for response transformation
5. **DTOs**: Use DTOs for complex data transfer between layers
6. **Events/Listeners**: Use events for loosely coupled actions
7. **Jobs**: Use jobs for long-running tasks
8. **Policy**: Use Laravel Policies for authorization
9. **Blade Components**: Reusable UI components

---

## 20. Testing Structure (tests/)

```
tests/
├── Feature/                    # Feature tests
│   ├── Auth/
│   ├── CampaignTest.php
│   ├── BrandTest.php
│   └── CreatorTest.php
│
├── Unit/                       # Unit tests
│   ├── Models/
│   └── Services/
│
├── TestCase.php                # Base test case
└── CreatesApplication.php     # Application creator for tests
```

---

## Summary

This project follows Laravel's MVC architecture with additional service and repository layers. The structure is designed for:

- **Multi-user platform**: Brands, Creators, Moderators, Admins
- **Campaign management**: Full CRUD with targeting
- **E-commerce features**: Orders, Payments, Payouts
- **Analytics**: Platform stats, Dashboard metrics
- **RESTful API**: Ready for mobile apps and integrations

All database tables have corresponding models, and the main entities (Brands, Creators, Campaigns) have complete service layers with business logic.
