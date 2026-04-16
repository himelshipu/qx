# Category System Reference (Admin Dashboard Canonical Pattern)

This document is the implementation blueprint for admin dashboard modules.
Use Category as the source pattern for structure, naming, layering, filtering, sorting, and UI interaction behavior.

## 1) Category File and Folder Structure (Reference First)

### 1.1 Backend Core Structure

```text
app/
	Http/
		Controllers/
			Backend/
				CategoryController.php
				CategoryFeaturedController.php
		Requests/
			Backend/
				Category/
					StoreCategoryRequest.php
					UpdateCategoryRequest.php
	Models/
		Category.php
	Repositories/
		Contracts/
			CategoryRepositoryInterface.php
		Eloquent/
			EloquentCategoryRepository.php
	Services/
		Admin/
			CategoryService.php
			CategoryFeaturedService.php

config/
	category.php

database/
	migrations/
		2026_03_29_000010_create_categories_table.php
		2026_04_16_120000_add_dashboard_indexes_to_categories_table.php
	seeders/
		CategorySeeder.php
```

### 1.2 Frontend Admin Structure

```text
resources/
	views/
		backend/
			pages/
				categories/
					index.blade.php
					_results.blade.php
					_form.blade.php
					create.blade.php
					edit.blade.php
	js/
		admin/
			categories-dashboard.js
```

### 1.3 Route Pattern in Dashboard Group

Category dashboard endpoints follow this naming:

1. dashboard.categories.index
2. dashboard.categories.table
3. dashboard.categories.create
4. dashboard.categories.store
5. dashboard.categories.edit
6. dashboard.categories.update
7. dashboard.categories.destroy
8. dashboard.categories.toggle-status

Featured modal endpoints are grouped separately:

1. dashboard.categories-featured.list
2. dashboard.categories-featured.add
3. dashboard.categories-featured.remove
4. dashboard.categories-featured.reorder
5. dashboard.categories-featured.search

## 2) Coding Pattern Used by Category Module

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

1. StoreXRequest
2. UpdateXRequest

Rules:

1. Constants must come from config (example: category.max_featured).
2. Do not duplicate hard-coded limits across layers.
3. Keep rules aligned with DB schema and service expectations.

### 2.4 Repository and Query Pattern

Required behavior shown in Category:

1. List query uses narrow select columns.
2. Dashboard counts use withCount for related usage metrics.
3. Filter methods are scope-driven (search/status/featured).
4. Ordering is centralized in a model scope.
5. Stats are cached and invalidated on mutations.

### 2.5 Service Pattern

Services must handle:

1. Slug creation and uniqueness behavior.
2. File upload and cleanup lifecycle.
3. Business guards before delete.
4. Status toggle behavior.
5. Reordering logic with transaction safety where needed.

### 2.6 Data and Schema Pattern

Migration rules:

1. Put base columns in original create table migration.
2. Keep performance indexes in dedicated index migration files.
3. Keep soft deletes in the original base migration for fresh installs.

Current category list-relevant indexes include:

1. is_active + deleted_at
2. is_featured + featured_order + deleted_at
3. name + deleted_at

## 3) Category Behavior to Mirror in Other Modules

### 3.1 Search and Filter

Category pattern:

1. Input search with debounce.
2. Select filters for status and other domain flags.
3. Reset link to clear filters.
4. AJAX table refresh via dedicated table endpoint.
5. URL query sync via history.replaceState.
6. AJAX pagination interception (no full page reload).

### 3.2 Sorting and Reordering

Category has two sort concepts:

1. List sort order for dashboard table: scopeDashboardOrder (currently by name).
2. Featured priority order: featured_order managed in modal reorder flow.

Rules:

1. For draggable reorder, send ordered IDs to a dedicated reorder endpoint.
2. Validate payload as integer ID array.
3. Update order in service/repository, not in controller.
4. Use transaction where multiple rows are updated.

### 3.3 Toggle Actions

Category pattern for status/featured toggles:

1. JS handles change events.
2. POST JSON to route template with CSRF header.
3. Update UI based on server response.
4. Revert checkbox on error.
5. Show user feedback toast.

## 4) Naming Pattern Standards

### 4.1 Files and Classes

Use singular domain in class names:

1. XController
2. XService
3. XRepositoryInterface
4. EloquentXRepository
5. StoreXRequest, UpdateXRequest

Blade folder uses plural resource name:

1. resources/views/backend/pages/xs

JS file naming:

1. xs-dashboard.js
2. x-form.js when form behavior is non-trivial

### 4.2 Routes and Permissions

Routes:

1. Prefix under dashboard group.
2. Route names follow dashboard.xs.action.

Permissions:

1. Use action-based slugs consistent with route intent.
2. Add new slugs to seeder before shipping module.
3. Ensure role assignment is updated for new slugs.

## 5) Delivery Checklist for Any New Admin Module

A module is not complete unless all items below are done.

1. Full folder layout matches this document.
2. Layering matches Controller -> Service -> Repository -> Model scopes.
3. No raw PHP blocks in Blade.
4. Search/filter are realtime and no-submit-button where applicable.
5. Partial table endpoint exists for AJAX filter refresh.
6. Pagination works without full reload.
7. Toggle actions are AJAX and rollback-safe.
8. Sort/reorder endpoint exists when ordering is business-relevant.
9. Config keys exist for shared constants.
10. Base migration + separate index migration are correctly split.
11. Seeder/permissions are updated for new actions.
12. JS module is imported in app entry and build passes.

## 6) AI Agent Instruction Block (Mandatory for Reuse)

This section is the operational instruction for any AI agent implementing a new admin feature using Category as reference.

### 6.1 Hard Rules

1. Replicate Category architecture exactly unless explicitly told otherwise.
2. Never place business/query logic in Blade.
3. Never use raw PHP blocks in Blade.
4. Never bypass service and repository layers from controllers.
5. Never hard-code limits that already belong in config.

### 6.2 Implementation Sequence (Must Follow in Order)

1. Create model scopes for search/filter/order.
2. Create repository interface and implementation.
3. Create service for business operations.
4. Create Store/Update FormRequest validation.
5. Create controller with index + table + CRUD + toggle + reorder (if needed).
6. Create Blade pages: index, _results, _form, create, edit.
7. Create dashboard JS module with realtime filter/search and AJAX interactions.
8. Add routes and permission slugs.
9. Add/adjust migrations and indexes.
10. Add/update seeders.
11. Wire JS import in app entry and run build.
12. Run diagnostics and fix all errors in touched files.

### 6.3 Search/Filter/Sort Requirements for New Modules

For every dashboard listing, the AI agent must deliver:

1. Debounced text search input.
2. Select-based status filter at minimum.
3. Reset action.
4. Partial table reload endpoint.
5. Query-string URL state sync.
6. AJAX pagination handling.
7. Stable default order scope.
8. Reorder endpoint and drag-drop UI when priority/order matters.

### 6.4 Done Criteria for AI Agent

Do not mark task complete unless all checks pass:

1. Architecture parity with Category is present.
2. Naming and folder structure parity is present.
3. Realtime filter/search behavior matches Category quality.
4. Sorting and reorder logic is implemented where required.
5. No Blade raw PHP exists in module views.
6. Build and diagnostics are clean for touched files.

If any item fails, continue implementation until fixed.
