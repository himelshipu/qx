# Campaign Architecture Reference (Admin + Frontend)

This file is the standard reference for campaign module implementation across admin dashboard and frontend/public domain.

## 1. Architecture Pattern

Use this layering for all new campaign features:

1. Controller (HTTP orchestration only)
2. Service Interface + Service Implementation (business rules and transitions)
3. Repository Interface + Repository Implementation (query composition, caching, persistence)
4. ViewModel/Data Transformer (UI-ready arrays; no view logic inside Blade)
5. Blade (declarative rendering only; no raw PHP blocks)

## 2. Controller Standard

Controller rules:

- Validate request.
- Authorize action.
- Delegate to service.
- Return view/redirect/json.
- Avoid embedding state machine and pricing logic directly in controller.

Campaign examples:

- Frontend negotiation actions are delegated to:
  - `App\Services\Frontend\Contracts\CampaignNegotiationServiceInterface`
  - `App\Services\Frontend\CampaignNegotiationService`
- Campaign show page UI prep is delegated to:
  - `App\ViewModels\Frontend\Campaign\CampaignShowViewModel`

## 3. Service + Interface Standard

Service interface is mandatory for cross-domain features.

Required behaviors for negotiation service:

- Influencer apply with initial offer.
- Brand accept/counter/decline.
- Influencer accept/counter/decline.
- Final agreed rate sync to campaign assignment (`campaign_influencers.agreed_amount`).
- Terminal state protection (approved/completed/declined/rejected).

When adding a new service interface, bind it in provider:

- `App\Providers\AppServiceProvider::register()`

## 4. Query and Load-Time Standard

Performance requirements:

- No query calls inside Blade (no relation method calls in loops).
- Use eager loading with minimal fields.
- Compute row metrics in service/viewmodel, not in template.
- Reuse prepared maps keyed by id (`applicationUi[application_id]`).
- Keep status transforms centralized.

Applied campaign optimization:

- Frontend campaign show now calculates follower/engagement/status metadata in `CampaignShowViewModel`.
- Blade reads prepared values only.
- Frontend campaign index query now uses constrained `select(...)`, safe role guards, and `withQueryString()` pagination.
- Frontend campaign display eager loads now fetch only required columns for applications, influencer relations, and targeting relations.

## 5. Blade Standard

Blade rules:

- No `@php` blocks.
- No database work.
- No heavy conditional mapping blocks that belong in view model.
- Use passed UI payload arrays for labels, classes, and display values.

Campaign frontend status:

- `resources/views/frontend/campaigns/designed-show.blade.php` has no raw PHP blocks.

## 6. Campaign Negotiation State Standard

Supported statuses:

- `invited`
- `applied`
- `countered_by_brand`
- `countered_by_influencer`
- `shortlisted`
- `approved`
- `rejected`
- `declined_by_brand`
- `declined_by_influencer`
- `completed`

Data fields:

- `influencer_offer`
- `brand_offer`
- `agreed_rate`
- `last_counter_by`
- `last_counter_at`
- `agreed_at`
- `declined_at`
- `declined_by`

## 7. Admin + Frontend Consistency Rules

- Any status introduced for frontend must be reflected in admin stats/filtering.
- Final agreed price source for campaign orders is assignment agreed amount.
- Keep route naming and permission mapping consistent with dashboard permission middleware.

## 8. Extension Checklist (Campaign)

Before merge:

1. Interface exists and is bound.
2. Controller contains no business branching beyond validation/auth.
3. Queries are eager-loaded and N+1 free.
4. Blade has no raw PHP blocks.
5. Status handling is symmetric for admin and frontend.
6. Order price source uses final agreed amount.
7. Diagnostics and migration checks pass.
