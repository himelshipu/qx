# Packages System Reference (Admin Dashboard Canonical Pattern)

This document defines the implementation blueprint for the admin Packages module under Commerce.
Use it as the canonical reference for file/folder structure, naming, layering, filtering, realtime table refresh, and permission mapping.

## 1) File and Folder Structure

### 1.1 Backend Core Structure

```text
app/
  Http/
    Controllers/
      Backend/
        PackageController.php
    Requests/
      Backend/
        Package/
          StorePackageRequest.php
          UpdatePackageRequest.php
  Models/
    Package.php
  Repositories/
    Contracts/
      PackageRepositoryInterface.php
    Eloquent/
      EloquentPackageRepository.php
  Services/
    Admin/
      PackageService.php
  Http/
    Middleware/
      EnforceDashboardRoutePermission.php
```

### 1.2 Frontend Admin Structure

```text
resources/
  views/
    backend/
      pages/
        packages/
          index.blade.php
          _results.blade.php
          create.blade.php
          edit.blade.php
          _form.blade.php
          view.blade.php
          purchase.blade.php
  js/
    admin/
      packages-dashboard.js
```

### 1.3 Route Pattern in Dashboard Group

Packages dashboard endpoints follow this naming:

1. dashboard.packages.index
2. dashboard.packages.table
3. dashboard.packages.create
4. dashboard.packages.store
5. dashboard.packages.view
6. dashboard.packages.edit
7. dashboard.packages.update
8. dashboard.packages.destroy
9. dashboard.packages.toggle-status
10. dashboard.packages.purchase
11. dashboard.packages.purchase.store

## 2) Coding Pattern Used by Packages Module

### 2.1 Mandatory Layering

Always follow this flow:

Controller -> Service -> Repository -> Model scopes

Rules:

1. Controller is orchestration only.
2. Service owns payload assembly and business rules.
3. Repository owns query composition and persistence.
4. Model owns reusable dashboard scopes.
5. FormRequest owns validation for create/update flows.

### 2.2 Blade and JS Pattern

Rules:

1. Keep page shell and filter bar in index.blade.php.
2. Keep table rows and pagination in _results.blade.php.
3. Keep dynamic filter/pagination/status behavior in packages-dashboard.js.
4. Dashboard listing should refresh without full page submit.

### 2.3 Repository and Query Pattern

Required behavior:

1. List query uses dashboard-specific scopes on the Package model.
2. Search is handled by a reusable model scope.
3. Status and platform filters are scope-driven.
4. Ordering is centralized in a model scope.
5. Stats are computed in repository methods from canonical counts.

### 2.4 Service Pattern

Services must handle:

1. Payload assembly for index and table views.
2. Platform option building for filters and forms.
3. Package create/update normalization.
4. Delete dependency checks and status toggle orchestration.
5. Purchase payload and purchase flow orchestration.

## 3) Packages Behavior to Mirror in Other Modules

### 3.1 Search and Filter

Packages pattern:

1. Debounced text search.
2. Select filters for platform and status.
3. Reset button to clear filters.
4. AJAX table refresh via dedicated table endpoint.
5. URL query sync via history.replaceState.
6. AJAX pagination interception without full reload.

### 3.2 Status Toggle Flow

Rules:

1. Toggle action is POST and returns JSON.
2. Toggle requests include CSRF token and X-Requested-With header.
3. On failure, UI checkbox state is reverted.
4. Success and failure feedback is shown through toast messages.

## 4) Permissions and Route Mapping

### 4.1 Permission Slugs

Packages slugs used by roles/seeders include:

1. packages.index
2. packages.create
3. packages.store
4. packages.view
5. packages.edit
6. packages.update
7. packages.toggle-status
8. packages.destroy
9. packages.purchase
10. packages.purchase.store

### 4.2 Table Route Permission Mapping

For dashboard middleware parity, map table route to index permission:

1. dashboard.packages.table -> packages.index

This keeps realtime table refresh authorized for users who can view packages.

## 5) Done Criteria for Packages Module

Do not mark the module complete unless all checks pass:

1. Folder and naming structure match this document.
2. Controller -> Service -> Repository -> Model scope layering is present.
3. AJAX table endpoint exists and returns _results partial.
4. Search/filter/reset/pagination work without full page reload.
5. Status toggle works after AJAX table refresh.
6. Table route permission mapping is implemented.
7. JS module is imported in the main app entry.
8. Diagnostics and build are clean for touched files.
