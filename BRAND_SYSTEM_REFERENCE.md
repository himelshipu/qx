# Brand System Reference (Admin Dashboard Canonical Pattern)

This document defines implementation rules for Brand management using Category-style architecture.

## 1) File and Folder Structure

### 1.1 Current Core Files

```text
app/
  Http/
    Controllers/
      Backend/
        BrandController.php
  Models/
    Brand.php
    BrandSocialLink.php
    BrandOnboardingProfile.php
  Repositories/
    Contracts/
      BrandRepositoryInterface.php
    Eloquent/
      EloquentBrandRepository.php
  Services/
    Admin/
      BrandService.php

config/
  brand.php

database/
  migrations/
    2026_03_29_000003_create_brands_table.php
    2026_03_29_000004_create_brand_social_links_table.php
    2026_03_29_000006_create_brand_onboarding_profiles_table.php
```

### 1.2 Frontend Admin Structure

```text
resources/
  views/
    backend/
      pages/
        brands/
          index.blade.php
          _results.blade.php
          _form.blade.php
          create.blade.php
          edit.blade.php
          view.blade.php
  js/
    admin/
      brands-dashboard.js
```

### 1.3 Route Pattern in Dashboard Group

Brand dashboard endpoints follow this naming:

1. dashboard.brands.index
2. dashboard.brands.table
3. dashboard.brands.create
4. dashboard.brands.store
5. dashboard.brands.view
6. dashboard.brands.edit
7. dashboard.brands.update
8. dashboard.brands.destroy
9. dashboard.brands.toggle-status
10. dashboard.brands.reorder

## 2) Required Pattern

### 2.1 Mandatory Layering

Always follow this flow:

Controller -> Service -> Repository -> Model scopes

Rules:
1. Controller is orchestration only.
2. Service owns business rules, side effects, and transactions.
3. Repository owns data access and query composition.
4. Model owns reusable scopes and relationships.
5. FormRequest owns validation.

### 2.2 Blade and JS Pattern

Rules:
1. No raw PHP blocks in Blade. Do not use @php...@endphp or <?php...?> in views.
2. Prepare all derived values in controller/service and pass to views.
3. Keep index layout in index.blade.php and table partial in _results.blade.php.
4. Keep page behavior in dedicated JS file under resources/js/admin.
5. Blade provides data-* route templates and tokens; JS consumes them.

### 2.3 Request Validation Pattern

Use module-specific FormRequest classes:

1. StoreBrandRequest
2. UpdateBrandRequest

Rules:
1. Constants must come from config (example: brand.max_featured).
2. Do not duplicate hard-coded limits across layers.
3. Keep rules aligned with DB schema and service expectations.

### 2.4 Repository and Query Pattern

Required behavior shown in Brand:

1. List query uses narrow select columns.
2. Dashboard counts use withCount for related usage metrics.
3. Filter methods are scope-driven (search/status).
4. Ordering uses model scope.
5. Stats are cached with TTL for performance.

### 2.5 Service Pattern

Services must handle:
1. Slug creation and uniqueness behavior.
2. File upload and cleanup lifecycle.
3. Business guards before delete (check dependencies).
4. Status toggle behavior.
5. Reordering logic with transaction safety.

## 3) Brand Behavior to Mirror in Other Modules

### 3.1 Search and Filter

Brand pattern:
1. Input search with debounce (350ms).
2. Select filters for status (active/inactive).
3. Reset link to clear filters.
4. AJAX table refresh via dedicated table endpoint.
5. URL query sync via history.replaceState.
6. AJAX pagination interception (no full page reload).
7. AbortController for cancelling pending requests.

### 3.2 Sorting and Reordering

Brand has two sort concepts:
1. List sort order for dashboard table: scopeDashboardOrder.
2. Drag-drop reorder via Sortable component with reorder endpoint.

Rules:
1. For draggable reorder, send ordered IDs to a dedicated reorder endpoint.
2. Validate payload as integer ID array.
3. Update order in service/repository, not in controller.
4. Use transaction where multiple rows are updated.

### 3.3 Toggle Actions

Brand pattern for status toggles:
1. JS handles change events on checkboxes.
2. POST JSON to route template with CSRF header.
3. Update UI based on server response.
4. Revert checkbox on error.
5. Show user feedback toast via window.toast.

### 3.4 Verified Badge

Brand has is_verified field separate from is_active:
- Display verified badge in results table
- Allow filtering by verified status
- Admin can toggle verified from edit form

## 4) Naming Pattern Standards

### 4.1 Files and Classes

Use singular domain in class names:

1. BrandController
2. BrandService
3. BrandRepositoryInterface
4. EloquentBrandRepository
5. StoreBrandRequest, UpdateBrandRequest

Blade folder uses plural resource name:
1. resources/views/backend/pages/brands

JS file naming:
1. brands-dashboard.js

### 4.2 Routes and Permissions

Routes:
1. Prefix under dashboard group.
2. Route names follow dashboard.brands.action.

Permissions (from seeder):
1. brands.index
2. brands.create
3. brands.show
4. brands.edit
5. brands.toggle-status
6. brands.destroy
7. brands.reorder

## 5) Delivery Checklist for Brand Refactor

A module is not complete unless all items below are done.

1. Full folder layout matches this document.
2. Layering matches Controller -> Service -> Repository -> Model scopes.
3. No raw PHP blocks in Blade.
4. Search/filter are realtime and no-submit-button where applicable.
5. Partial table endpoint exists for AJAX filter refresh.
6. Pagination works without full reload.
7. Toggle actions are AJAX and rollback-safe.
8. Sort/reorder endpoint exists and drag-drop works.
9. Config keys exist for shared constants.
10. Model has proper dashboard scopes (searchDashboard, filterStatus, dashboardOrder).
11. Stats caching implemented for performance.
12. JS module is imported in app entry and build passes.
13. Run diagnostics and fix all errors in touched files.

## 6) Performance Optimizations

### 6.1 Query Optimization

Brand system optimizations:
1. Narrow select columns in paginateForDashboard
2. use with() for eager loading relationships (user, counts)
3. use withCount() instead of separate counts
4. Stats cached with 300s TTL
5. AbortController for cancelling pending Ajax requests
6. Debounced search (350ms delay)

### 6.2 Caching Strategy

Stats caching:
- Key: 'dashboard:brands:stats'
- TTL: 300 seconds
- Invalidated on create/update/delete/toggle

### 6.3 Index Usage

Key indexes for brands:
1. user_id on brands table
2. is_active + deleted_at
3. sort_order for reorder

## 7) AI Agent Instruction Block (Mandatory for Implementation)

### 7.1 Hard Rules

1. Replicate Brand architecture exactly unless explicitly told otherwise.
2. Never place business/query logic in Blade.
3. Never use raw PHP blocks in Blade.
4. Never bypass service and repository layers from controllers.
5. Never hard-code limits that already belong in config.

### 7.2 Implementation Sequence (Must Follow in Order)

1. Add model scopes for search/filter/order in Brand.
2. Create BrandRepositoryInterface and EloquentBrandRepository.
3. Create BrandService for business operations.
4. Create StoreBrandRequest and UpdateBrandRequest validation.
5. Refactor BrandController with service/repository.
6. Refactor Blade pages: index, _results, _form, create, edit, view.
7. Refactor brands-dashboard.js for realtime filter/search and AJAX interactions.
8. Add routes for table endpoint and reorder endpoint.
9. Add configurable stats caching.
10. Wire JS import in app entry and run build.
11. Run diagnostics and fix all errors in touched files.

### 7.3 Done Criteria for AI Agent

Do not mark task complete unless all checks pass:
1. Architecture parity with Brand pattern is present.
2. Naming and folder structure parity is present.
3. Realtime filter/search behavior is implemented.
4. Sorting and reorder logic is implemented.
5. No Blade raw PHP exists in brand views.
6. Build and diagnostics are clean for touched files.
7. Stats caching is implemented for performance.

If any item fails, continue implementation until fixed.