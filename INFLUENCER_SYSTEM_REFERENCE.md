# Influencer System Reference (Admin Dashboard Canonical Pattern)

This document defines implementation rules for Influencer management using Category-style architecture.

## 1) File and Folder Structure

### 1.1 Backend Core Structure

```text
app/
  Http/
    Controllers/
      Backend/
        InfluencerController.php
    Requests/
      Backend/
        Influencer/
          StoreInfluencerRequest.php
          UpdateInfluencerRequest.php
  Models/
    Influencer.php
  Repositories/
    Contracts/
      InfluencerRepositoryInterface.php
    Eloquent/
      EloquentInfluencerRepository.php
  Services/
    Admin/
      InfluencerService.php
```

### 1.2 Frontend Admin Structure

```text
resources/
  views/
    backend/
      pages/
        influencers/
          index.blade.php
          _results.blade.php
          _form.blade.php
          create.blade.php
          edit.blade.php
          view.blade.php
  js/
    admin/
      influencers-dashboard.js
```

### 1.3 Route Pattern in Dashboard Group

Influencer dashboard endpoints:

1. dashboard.influencers.index
2. dashboard.influencers.table
3. dashboard.influencers.create
4. dashboard.influencers.store
5. dashboard.influencers.view
6. dashboard.influencers.edit
7. dashboard.influencers.update
8. dashboard.influencers.destroy
9. dashboard.influencers.toggle-status
10. dashboard.influencers.toggle-featured
11. dashboard.influencers-featured.list
12. dashboard.influencers-featured.add
13. dashboard.influencers-featured.remove
14. dashboard.influencers-featured.reorder
15. dashboard.influencers-featured.search

## 2) Required Pattern

### 2.1 Mandatory Layering

Always follow:

Controller -> Service -> Repository -> Model scopes

Rules:
1. Controller is orchestration only.
2. Service owns business rules and transactions.
3. Repository owns query composition and persistence.
4. Model owns reusable dashboard scopes.
5. FormRequest owns validation.

### 2.2 Blade and JS Pattern

Rules:
1. No raw PHP blocks in Blade templates.
2. Use index.blade.php for shell and _results.blade.php for table fragment.
3. Use resources/js/admin/influencers-dashboard.js for page behavior.
4. Use data-* attributes for route templates and CSRF token.
5. Keep filters, toggles, pagination, and featured modal interactions in JS/AJAX flow.

### 2.3 Repository and Query Pattern

Required behavior:
1. paginateForDashboard uses model scopes: forDashboard, searchDashboard, filterStatus, dashboardFeatured, dashboardOrder.
2. getStats is cached and invalidated after mutations.
3. syncCategories invalidates cache because categorized stats depend on pivot state.
4. Featured position uses featured_priority and validated ordered ID payload.

## 3) Dashboard Behavior

### 3.1 Search and Filter

1. Debounced search by display name/title/user/category.
2. Status filter: all/active/inactive.
3. Featured filter: all/featured/non-featured.
4. AJAX table endpoint refresh.
5. URL query sync and AJAX pagination interception.

### 3.2 Featured Position Modal

1. Top Feature Position button opens the modal.
2. Modal supports search, add, remove, and drag reorder of featured influencers.
3. Reorder payload posts ordered influencer IDs to dashboard.influencers-featured.reorder.
4. On success/failure, show toast and keep modal list consistent.

### 3.3 Toggle Actions

1. Status toggle via AJAX to dashboard.influencers.toggle-status.
2. Featured toggle via AJAX to dashboard.influencers.toggle-featured.
3. Toggle rollback on API error.
4. Toast feedback for success/error.

## 4) Naming Standards

1. Controller: InfluencerController
2. Service: InfluencerService
3. Repository: InfluencerRepositoryInterface, EloquentInfluencerRepository
4. Request classes: StoreInfluencerRequest, UpdateInfluencerRequest
5. JS module: influencers-dashboard.js
6. Views folder: resources/views/backend/pages/influencers

## 5) Delivery Checklist

1. index.blade.php uses dashboard root data-* attributes.
2. _results.blade.php is used by both initial render and table AJAX response.
3. Controller exposes both index and table endpoints.
4. Search/filter/pagination are AJAX and URL-synced.
5. Featured position modal works via list/add/remove/reorder/search endpoints.
6. Status and featured toggles are AJAX + rollback-safe.
7. Stats cache invalidates on create/update/delete/toggle/category-sync.
8. JS module is imported in resources/js/app.js.
