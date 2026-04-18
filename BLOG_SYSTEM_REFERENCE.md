# Blog System Reference (Admin Dashboard Canonical Pattern)

This document defines implementation rules for Blog Posts using Category-style architecture.

## 1) File and Folder Structure

### 1.1 Current Core Files (After Refactor)

```text
app/
  Http/
    Controllers/
      Backend/
        BlogController.php
    Requests/
      Backend/
        Blog/
          StoreBlogPostRequest.php
          UpdateBlogPostRequest.php
  Models/
    BlogPost.php
  Repositories/
    Contracts/
      BlogPostRepositoryInterface.php
    Eloquent/
      EloquentBlogPostRepository.php
  Services/
    Admin/
      BlogPostService.php

config/
  blog.php

database/
  migrations/
    2026_04_13_000100_create_blog_posts_table.php
```

### 1.2 Frontend Admin Structure

```text
resources/
  views/
    backend/
      pages/
        blog/
          index.blade.php
          _results.blade.php
          _form.blade.php
          create.blade.php
          edit.blade.php
          show.blade.php
  js/
    admin/
      blog-dashboard.js
```

### 1.3 Route Pattern in Dashboard Group

Blog dashboard endpoints follow this naming:

1. dashboard.blogs.index
2. dashboard.blogs.table
3. dashboard.blogs.create
4. dashboard.blogs.store
5. dashboard.blogs.show
6. dashboard.blogs.edit
7. dashboard.blogs.update
8. dashboard.blogs.toggle-status
9. dashboard.blogs.toggle-featured
10. dashboard.blogs.destroy
11. dashboard.blogs.restore
12. dashboard.blogs.reorder

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

1. StoreBlogPostRequest
2. UpdateBlogPostRequest

Rules:
1. Constants must come from config (example: blog.title_max).
2. Do not duplicate hard-coded limits across layers.
3. Keep rules aligned with DB schema and service expectations.

### 2.4 Repository and Query Pattern

Required behavior shown in Blog:

1. List query uses narrow select columns with author relationship.
2. Dashboard counts use raw queries for stats.
3. Filter methods are scope-driven (search/status/published/draft/trashed).
4. Ordering uses model scope.
5. Include soft delete handling (trashed state).

### 2.5 Service Pattern

Services must handle:
1. Slug creation and uniqueness behavior.
2. File upload and cleanup lifecycle.
3. Business guards before delete.
4. Status/featured toggle behavior.
5. Published_at timestamp logic.

## 3) Blog Behavior to Implement

### 3.1 Search and Filter

Blog pattern:
1. Input search with debounce.
2. Select filters for status (published/draft/trashed).
3. Reset link to clear filters.
4. AJAX table refresh via dedicated table endpoint.
5. URL query sync via history.replaceState.
6. AJAX pagination interception (no full page reload).

### 3.2 Sorting and Ordering

Blog has sort_order column for manual ordering in lists.
1. Drag-drop reorder not required currently.
2. Default order: sort_order first, then published_at desc.

### 3.3 Toggle Actions

Blog supports multiple toggles:
1. Toggle publish status.
2. Toggle featured status.
3. JS handles change events with POST to route templates.
4. Update UI based on server response.
5. Revert checkbox on error.
6. Show user feedback toast.

## 4) Naming Pattern Standards

### 4.1 Files and Classes

Use singular domain in class names:

1. BlogPostController (or BlogController for legacy compatibility)
2. BlogPostService
3. BlogPostRepositoryInterface
4. EloquentBlogPostRepository
5. StoreBlogPostRequest, UpdateBlogPostRequest

Blade folder uses plural resource name:
1. resources/views/backend/pages/blogs

JS file naming:
1. blog-dashboard.js

### 4.2 Routes and Permissions

Routes:
1. Prefix under dashboard group.
2. Route names follow dashboard.blogs.action.

Permissions (from seeder):
1. blogs.index
2. blogs.create
3. blogs.show
4. blogs.edit
5. blogs.toggle-status
6. blogs.toggle-featured
7. blogs.destroy
8. blogs.restore
9. blogs.reorder

## 5) Delivery Checklist for Blog Refactor

A module is not complete unless all items below are done.

1. Full folder layout matches this document.
2. Layering matches Controller -> Service -> Repository -> Model scopes.
3. No raw PHP blocks in Blade.
4. Search/filter are realtime and no-submit-button where applicable.
5. Partial table endpoint exists for AJAX filter refresh.
6. Pagination works without full reload.
7. Toggle actions are AJAX and rollback-safe.
8. Config keys exist for shared constants.
9. Model has proper dashboard scopes (forDashboard, dashboardStatus, dashboardOrder).
10. Service handles slug building and file cleanup.
11. JS module is imported in app entry and build passes.
12. Run diagnostics and fix all errors in touched files.

## 6) AI Agent Instruction Block (Mandatory for Implementation)

### 6.1 Hard Rules

1. Replicate Blog architecture exactly unless explicitly told otherwise.
2. Never place business/query logic in Blade.
3. Never use raw PHP blocks in Blade.
4. Never bypass service and repository layers from controllers.
5. Never hard-code limits that already belong in config.

### 6.2 Implementation Sequence (Must Follow in Order)

1. Add model scopes for search/filter/order in BlogPost.
2. Create BlogPostRepositoryInterface and EloquentBlogPostRepository.
3. Create BlogPostService for business operations.
4. Create StoreBlogPostRequest and UpdateBlogPostRequest validation.
5. Refactor BlogController with index + table + CRUD + toggle + restore.
6. Refactor Blade pages: index, _results, _form, create, edit, show.
7. Create blog-dashboard.js for realtime filter/search and AJAX interactions.
8. Add/update routes for new table endpoint.
9. Add config keys to blog.php if needed.
10. Wire JS import in app entry and run build.
11. Run diagnostics and fix all errors in touched files.

### 6.3 Done Criteria for AI Agent

Do not mark task complete unless all checks pass:
1. Architecture parity with Blog pattern is present.
2. Naming and folder structure parity is present.
3. Realtime filter/search behavior is implemented.
4. Toggle actions are AJAX with rollback-safe behavior.
5. No Blade raw PHP exists in blog views.
6. Build and diagnostics are clean for touched files.

If any item fails, continue implementation until fixed.