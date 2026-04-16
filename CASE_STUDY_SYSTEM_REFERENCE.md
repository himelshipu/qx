# Case Study System Reference (Admin Dashboard Canonical Pattern)

This document is the implementation blueprint for Case Studies in the admin dashboard.
Use it as the canonical reference for architecture, naming, filtering, search, sorting, and reusable implementation rules.

## 1) Case Study File and Folder Structure (Reference First)

### 1.1 Backend Core Structure

```text
app/
  Http/
    Controllers/
      Backend/
        CaseStudyController.php
      Frontend/
        CaseStudyController.php
    Requests/
      Backend/
        CaseStudy/
          StoreCaseStudyRequest.php
          UpdateCaseStudyRequest.php
  Models/
    CaseStudy.php
  Repositories/
    Contracts/
      CaseStudyRepositoryInterface.php
    Eloquent/
      EloquentCaseStudyRepository.php
  Services/
    Admin/
      CaseStudyService.php

config/
  case-study.php

database/
  migrations/
    2026_03_29_000045_create_case_studies_table.php
  seeders/
    CaseStudySeeder.php
```

### 1.2 Frontend Admin Structure

```text
resources/
  views/
    backend/
      pages/
        case-studies/
          index.blade.php
          _form.blade.php
          create.blade.php
          edit.blade.php
          show.blade.php
    frontend/
      pages/
        case-studies/
          case-studies.blade.php
          show.blade.php
  js/
    admin/
      case-studies-dashboard.js
      case-study-form.js
```

### 1.3 Route Pattern in Dashboard Group

Case Studies dashboard endpoints follow this naming:

1. dashboard.case-studies.index
2. dashboard.case-studies.create
3. dashboard.case-studies.store
4. dashboard.case-studies.show
5. dashboard.case-studies.edit
6. dashboard.case-studies.update
7. dashboard.case-studies.destroy
8. dashboard.case-studies.toggle-status
9. dashboard.case-studies.reorder

Permission-protected action currently includes:

1. case-studies.reorder

## 2) Coding Pattern Used by Case Study Module

### 2.1 Mandatory Layering

Always follow this flow:

Controller -> Service -> Repository -> Model scopes

Rules:

1. Controller handles request parsing and response orchestration only.
2. Service handles business logic, file lifecycle, slug strategy, and write workflows.
3. Repository handles database operations and query composition.
4. Model provides reusable scopes for listing/filter/search/order.
5. FormRequest owns validation and config-bound limits.

### 2.2 Blade and JS Pattern

Rules:

1. No raw PHP blocks in Blade. Do not use @php...@endphp or <?php...?>.
2. Pass prepared view data from controller/service.
3. Keep listing screen behavior in case-studies-dashboard.js.
4. Keep form uploader/preview behavior in case-study-form.js.
5. Use data-* route templates and CSRF token attributes in dashboard root.

### 2.3 Request Validation Pattern

Use module-specific FormRequest classes:

1. StoreCaseStudyRequest
2. UpdateCaseStudyRequest

Rules:

1. Keep shared limits in config/case-study.php.
2. Validate cover image size and mime types via config value.
3. Keep sort_order integer validation aligned with DB behavior.
4. Keep URL and publish flags normalized before persistence.

### 2.4 Repository and Query Pattern

Required behavior shown in Case Studies:

1. Dashboard list query uses model scopes: forDashboard, search, dashboardStatus, dashboardOrder.
2. Stats are returned as total/published/draft.
3. Slug existence checks support ignoreId for updates.
4. Sort reorder uses ordered IDs and single SQL CASE update.
5. Publish toggle writes is_published and published_at atomically.

### 2.5 Service Pattern

Services must handle:

1. Listing payload composition for dashboard.
2. Sort-order defaulting via repository getNextSortOrder.
3. Unique slug generation and collision fallback.
4. Cover image storage and cleanup from public disk.
5. Publish date lifecycle when status changes.
6. Reorder input normalization before repository write.

### 2.6 Data and Schema Pattern

Current base schema for case_studies includes:

1. title, slug, summary
2. cover_image_path, external_url
3. is_published, sort_order, published_at
4. timestamps

Migration rules for future modules:

1. Keep base columns in the original create migration.
2. Keep performance indexes in separate index migrations.
3. Keep soft deletes in base migration for fresh installs when possible.

## 3) Case Study Behavior to Mirror in Other Modules

### 3.1 Search and Filter

Current Case Studies dashboard pattern:

1. Debounced search input.
2. Status select filter.
3. Reset action.
4. AJAX refresh of listing area.
5. URL query synchronization.
6. AJAX pagination interception.

### 3.2 Sorting and Reordering

Case Studies ordering pattern:

1. Model default dashboard order uses sort_order then published_at.
2. Dashboard supports drag-drop row reordering.
3. Reorder endpoint accepts ordered IDs and persists via CASE update.
4. UI order labels are updated optimistically after drag-drop.

### 3.3 Toggle Actions

Case Studies status toggle pattern:

1. JS listens for status switch changes.
2. POST JSON request to route template with CSRF headers.
3. Checkbox state is synced from server response.
4. On failure, checkbox state is rolled back.
5. Listing is refreshed after successful toggle to keep row state consistent.

## 4) Naming Pattern Standards

### 4.1 Files and Classes

Use singular domain in class names:

1. CaseStudyController
2. CaseStudyService
3. CaseStudyRepositoryInterface
4. EloquentCaseStudyRepository
5. StoreCaseStudyRequest and UpdateCaseStudyRequest

Blade folder uses plural resource name:

1. resources/views/backend/pages/case-studies

JS naming:

1. case-studies-dashboard.js for list page behavior
2. case-study-form.js for create/edit form behavior

### 4.2 Routes and Permissions

Routes:

1. Keep all admin case study routes under dashboard prefix.
2. Keep names as dashboard.case-studies.action.

Permissions:

1. Keep slugs aligned to route intent (case-studies.*).
2. Add new slugs in PermissionSeeder before shipping route changes.
3. Ensure role assignment includes newly introduced slugs.

## 5) Delivery Checklist for Any New Module Reusing Case Study Pattern

A module is not complete unless all items below are done.

1. Full folder and naming structure matches this guide.
2. Layering is Controller -> Service -> Repository -> Model scopes.
3. No raw PHP blocks in Blade.
4. Realtime search/filter works without submit button where applicable.
5. AJAX pagination works without full reload.
6. Toggle actions are AJAX with rollback-safe behavior.
7. Reorder endpoint and drag-drop are implemented when ordering matters.
8. Config keys exist for all shared limits.
9. Seeder includes permissions for protected actions.
10. JS modules are imported in app entry and build passes.
11. Diagnostics are clean for touched files.

## 6) AI Agent Instruction Block (Mandatory for Reuse)

This section is the operational instruction for AI agents implementing any admin feature using Case Studies as reference.

### 6.1 Hard Rules

1. Follow Case Study architecture parity unless explicitly overridden.
2. Never put query/business logic in Blade.
3. Never use raw PHP blocks in Blade templates.
4. Never bypass service and repository from controllers.
5. Never duplicate constants across request/service/controller; use config.

### 6.2 Mandatory Implementation Sequence

1. Create model scopes for dashboard listing, search, status filter, and default order.
2. Create repository interface and Eloquent implementation.
3. Create service class for business logic and side effects.
4. Create Store/Update FormRequest classes.
5. Create controller with index + CRUD + toggle + reorder (if required).
6. Create backend blades: index, _form, create, edit, show.
7. Create admin JS modules for dashboard and form interactions.
8. Wire routes and permission slugs.
9. Create or update migrations and indexes.
10. Update seeders and permission-role mappings.
11. Import JS modules in app entry and run build.
12. Run diagnostics and resolve all touched-file issues.

### 6.3 Search, Filter, Sort, and Reorder Requirements

For every dashboard listing, AI agent must implement:

1. Debounced text search.
2. Select-based status filter.
3. Reset action.
4. AJAX listing refresh.
5. URL query-state sync.
6. AJAX pagination handling.
7. Stable default order scope.
8. Reorder endpoint and drag-drop when order is business-relevant.

### 6.4 Done Criteria for AI Agent

Do not mark complete unless all checks pass:

1. Architecture parity with this guide is present.
2. File/folder naming parity is present.
3. Search/filter behavior is realtime and consistent.
4. Sorting and reorder behavior is implemented where required.
5. No Blade raw PHP exists in module views.
6. Build and diagnostics are clean for touched files.

If any check fails, continue implementation until fixed.
