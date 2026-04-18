# Brand System Reference (Admin Dashboard Canonical Pattern)

This document defines implementation rules for Brand management using Category-style architecture.

## 1) File and Folder Structure

### 1.1 Backend Core Structure

```text
app/
  Http/
    Controllers/
      Backend/
        BrandController.php
        BrandFeaturedController.php
    Requests/
      Backend/
        Brand/
          StoreBrandRequest.php
          UpdateBrandRequest.php
  Models/
    Brand.php
  Repositories/
    Contracts/
      BrandRepositoryInterface.php
    Eloquent/
      EloquentBrandRepository.php
  Services/
    Admin/
      BrandService.php
      BrandFeaturedService.php

config/
  brand.php
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

Brand dashboard endpoints:

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

Featured modal endpoints:

1. dashboard.brands-featured.list
2. dashboard.brands-featured.add
3. dashboard.brands-featured.remove
4. dashboard.brands-featured.reorder
5. dashboard.brands-featured.search

## 2) Required Pattern

### 2.1 Mandatory Layering

Always follow:

Controller -> Service -> Repository -> Model scopes

Rules:
1. Controller is orchestration only.
2. Service owns business rules, side effects, and transactions.
3. Repository owns data access and query composition.
4. Model owns reusable scopes and relationships.
5. FormRequest owns validation.

### 2.2 Blade and JS Pattern

Rules:
1. No raw PHP blocks in Blade.
2. Keep index layout in index.blade.php and table rows in _results.blade.php.
3. Keep dashboard behavior in resources/js/admin/brands-dashboard.js.
4. Use data-* route templates from Blade and consume in JS.
5. Use AJAX filtering and pagination interception without full page reload.

### 2.3 Repository and Query Pattern

Required behavior:
1. Use narrow select columns for listing.
2. Use model scopes for search/filter/order.
3. Cache stats and invalidate on mutations.
4. Keep featured ordering updates transactional.

## 3) Dashboard Behavior

### 3.1 Search and Filter

1. Debounced search input.
2. Status filter select.
3. Reset action.
4. AJAX table endpoint refresh.
5. URL query sync via history.replaceState.

### 3.2 Featured Position Management

1. Feature Position button opens modal.
2. Search brands in modal.
3. Add/remove featured brands.
4. Drag to reorder featured brands.
5. Persist order through dedicated reorder endpoint.

### 3.3 Toggle Actions

1. Status toggle via AJAX.
2. Featured toggle via AJAX.
3. Revert toggle UI on failure.
4. Show toast feedback for success/failure.

## 4) Naming Standards

1. Controller: BrandController, BrandFeaturedController
2. Service: BrandService, BrandFeaturedService
3. Repository: BrandRepositoryInterface, EloquentBrandRepository
4. Request classes: StoreBrandRequest, UpdateBrandRequest
5. JS module: brands-dashboard.js
6. Views folder: resources/views/backend/pages/brands

## 5) Delivery Checklist

1. Layering matches Controller -> Service -> Repository -> Model scopes.
2. No raw PHP blocks in dashboard blades.
3. Table endpoint exists and returns _results partial HTML payload.
4. Search/filter/pagination are AJAX and URL-synced.
5. Featured modal flow (list/add/remove/search/reorder) is functional.
6. Stats cache is invalidated on all brand mutations.
7. JS module is imported in resources/js/app.js.
