# Static Page System Reference (Admin Dashboard Canonical Pattern)

This document defines implementation rules for Static Pages in the admin dashboard.
Use it as the canonical reference for file/folder structure, naming, layering, and dashboard behaviors.

## 1) File and Folder Structure

### 1.1 Implemented Core Files

```text
app/
  Http/
    Controllers/
      Backend/
        StaticPageController.php
    Requests/
      Backend/
        StaticPage/
          StoreStaticPageRequest.php
          UpdateStaticPageRequest.php
  Models/
    StaticPage.php
  Repositories/
    Contracts/
      StaticPageRepositoryInterface.php
    Eloquent/
      EloquentStaticPageRepository.php
  Services/
    Admin/
      StaticPageService.php

config/
  static-page.php

resources/
  js/
    admin/
      static-pages-dashboard.js
      static-page-form.js
  views/
    backend/
      pages/
        static-pages/
          _form.blade.php
          _results.blade.php
          index.blade.php
          create.blade.php
          edit.blade.php
          show.blade.php

routes/
  web.php
```

### 1.2 Optional Enhancements Structure

```text
app/
  Http/
    Controllers/
      Backend/
        StaticPageController.php
    Requests/
      Backend/
        StaticPage/
          StoreStaticPageRequest.php
          UpdateStaticPageRequest.php
          ToggleStaticPageStatusRequest.php
  Models/
    StaticPage.php
  Repositories/
    Contracts/
      StaticPageRepositoryInterface.php
    Eloquent/
      EloquentStaticPageRepository.php
  Services/
    Admin/
      StaticPageService.php
      StaticPageSeoService.php

config/
  static-page.php

resources/
  views/
    backend/
      pages/
        static-pages/
          index.blade.php
          _results.blade.php
          _form.blade.php
          create.blade.php
          edit.blade.php
          show.blade.php
  js/
    admin/
      static-pages-dashboard.js
      static-page-form.js
      static-page-seo-preview.js
```

## 2) Required Pattern

1. Keep Controller -> Service -> Repository -> Model scope layering.
2. Move inline validation from controller to FormRequest classes.
3. Move query composition and stats aggregation to repository.
4. Keep slug strategy, publish/status rules, and mutation workflow in service.
5. Add model scopes for dashboard list, search, status filter, and default order.
6. Keep Blade templates free from query/business logic.

## 3) Required Dashboard Behavior

1. Realtime search and status filter on listing page.
2. AJAX partial results endpoint for listing refresh.
3. AJAX status toggle with rollback-safe UI behavior.
4. AJAX pagination interception to avoid full page reload.
5. URL query synchronization for filter state.
6. Dedicated dashboard JS module wired through app entry.

## 4) Route and Naming Pattern

### 4.1 Required Dashboard Routes

1. dashboard.static-pages.index
2. dashboard.static-pages.table
3. dashboard.static-pages.create
4. dashboard.static-pages.store
5. dashboard.static-pages.show
6. dashboard.static-pages.edit
7. dashboard.static-pages.update
8. dashboard.static-pages.destroy
9. dashboard.static-pages.toggle-status
10. dashboard.static-pages.preview-slug (optional)

### 4.2 Naming Rules

1. Use singular domain in class names: StaticPageController, StaticPageService.
2. Use plural view folder: backend/pages/static-pages.
3. Use dashboard route prefix and action-based route names.
4. Keep permission slugs aligned to actions: static-pages.index, static-pages.create, static-pages.edit, static-pages.destroy, static-pages.toggle-status.

## 5) Static Page Domain Rules

1. Title and slug must remain unique.
2. Slug normalization and conflict handling should be centralized in service.
3. Content is required and supports rich text.
4. SEO fields (meta_description, meta_keywords) are optional and length-limited.
5. is_active controls published/draft visibility in dashboard and frontend.
6. Soft delete must be preserved and respected in all list queries.

## 6) Validation Rules Source

1. Create config/static-page.php for shared constraints (title max, slug max, content limits, meta limits, per-page, debounce).
2. FormRequest classes must read limits from config/static-page.php.
3. Avoid hard-coded validation numbers in controller, service, or repository.

## 7) Controller Rules

1. Controller orchestrates only.
2. No direct Eloquent query composition in controller methods.
3. No inline $request->validate blocks in controller.
4. No response-building side logic beyond orchestration.

## 8) Repository Rules

1. Handle listing queries, search/status filtering, sorting, pagination, and stats.
2. Provide CRUD accessors for service layer.
3. Provide slug existence checks with ignore-id support.
4. Provide status toggle persistence and return refreshed model state.

## 9) Service Rules

1. Build listing payload for index and table responses.
2. Handle create/update workflows and normalization.
3. Handle slug generation policy and collision fallback.
4. Handle status toggle orchestration.
5. Keep business safeguards centralized.

## 10) Blade and JS Rules

1. Use _results.blade.php for list table body/card list.
2. Keep index.blade.php as page shell and filter form host.
3. Do not use raw PHP blocks in blade templates.
4. Move inline scripts to static-pages-dashboard.js and static-page-form.js.
5. Pass route templates and csrf token through data-* attributes.

## 11) AI Agent Instruction Block

1. Never keep monolithic CRUD logic in StaticPageController.
2. Never place validation/query/business logic in blade files.
3. Never bypass service and repository from controller.
4. Always use FormRequest for store/update operations.
5. Add _results partial and table endpoint before marking listing complete.
6. Ensure app.js imports static-pages-dashboard.js and static-page-form.js.
7. Run diagnostics on all touched files before completion.

## 12) Delivery Checklist

- [x] Static page module follows Controller -> Service -> Repository -> Model scopes.
- [x] StoreStaticPageRequest and UpdateStaticPageRequest exist and are used.
- [x] static-page.php config exists and validation limits are config-driven.
- [x] Index page has realtime search/filter and AJAX table refresh.
- [x] _results.blade.php exists and pagination works via AJAX.
- [x] Status toggle is AJAX + rollback-safe.
- [x] No blade raw PHP blocks in static page views.
- [x] Routes include dashboard.static-pages.table.
- [x] JS modules are imported in app entry.
- [x] Diagnostics are clean for touched files.
