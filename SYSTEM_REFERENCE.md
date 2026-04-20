# System Reference

This is the single system reference for the application. It replaces the fragmented module-level `*_SYSTEM_REFERENCE.md` files.

## Platform Overview

- Backend: Laravel 12 / PHP
- Frontend: Blade templates with Alpine.js for interactive UI
- Access model: dashboard users vs frontend brand/influencer users
- Main domains: campaigns, packages, orders, payments, reviews, support, content, messaging, RBAC

## Core Data Model

- `Campaign` and `CampaignApplication` drive campaign negotiation and participation.
- `CampaignInfluencer` stores final approved influencer assignment and agreed amount.
- `Order` is the checkout container.
- `OrderItem` handles package workflow tasks.
- `SubOrder` handles campaign workflow tasks.
- `Review` stores immutable public reviews for brand and influencer.
- `Conversation` and `OrderMessage` handle messaging.
- `Payment` and payout fields track settlement state.

## Workflow Summary

### Campaign Flow

1. Influencer applies or is invited.
2. Brand accepts, counters, or declines.
3. When approved, a campaign order and `SubOrder` are created.
4. Influencer moves task status forward.
5. Brand approves or rejects delivered work.
6. Influencer can rate the brand after approval/completion.

### Package Flow

1. Brand checks out package tasks.
2. Parent order and child orders are created.
3. Influencer updates task progress.
4. Brand approves or rejects delivered items.
5. Brand and influencer can leave reviews after completion where allowed.

## Role Boundaries

- `admin`, `superadmin`, `moderator`, and `manager` use the dashboard.
- `brand` and `influencer` stay in the frontend area.
- Dashboard access must remain blocked for `brand` and `influencer` users.
- Admin can act on behalf of either side in workflow decisions when needed.

## Status Rules

- Package task canonical statuses: `pending`, `in_progress`, `delivered`, `approved`, `rejected`, `cancelled`, `completed`.
- Campaign task canonical statuses: `pending`, `in_progress`, `delivered`, `approved`, `rejected`, `cancelled`.
- Normal actors move forward only.
- Admin override is allowed only where explicitly implemented.
- Display layers should normalize legacy labels instead of changing the stored workflow unexpectedly.

## UI and Code Conventions

- Controllers validate, authorize, and delegate.
- View models prepare UI payloads and labels.
- Blade stays declarative and should not contain database logic.
- Eager load data before rendering.
- Keep role-specific UI separate from shared workflow UI.
- Keep campaign and package workflow rendering aligned with the same task-state vocabulary.

## Key Areas To Check When Changing Features

- `app/Http/Controllers/Frontend/CampaignController.php`
- `app/Http/Controllers/Frontend/CampaignApplicationController.php`
- `app/Http/Controllers/Frontend/OrderController.php`
- `app/ViewModels/Frontend/Campaign/CampaignShowViewModel.php`
- `resources/views/frontend/campaigns/designed-show.blade.php`
- `resources/views/frontend/orders/show.blade.php`
- `routes/web.php`

## Maintenance Rule

- Do not recreate per-module system reference files.
- Add new workflows only when they fit this consolidated model.