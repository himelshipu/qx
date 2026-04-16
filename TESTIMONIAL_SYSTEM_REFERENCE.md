# Testimonial System Reference (Admin Dashboard Canonical Pattern)

This document is the implementation blueprint for Testimonials in the admin dashboard.
Use it as the source of truth for architecture, naming, filtering, sorting, and reusable AI implementation behavior.

## 1) Testimonial File and Folder Structure (Reference First)

### 1.1 Backend Core Structure

```text
app/
  Http/
    Controllers/
      Backend/
        TestimonialController.php
    Requests/
      Backend/
        Testimonial/
          StoreTestimonialRequest.php
          UpdateTestimonialRequest.php
  Models/
    Testimonial.php
  Repositories/
    Contracts/
      TestimonialRepositoryInterface.php
    Eloquent/
      EloquentTestimonialRepository.php
  Services/
    Admin/
      TestimonialService.php
  Providers/
    RepositoryServiceProvider.php

config/
  testimonial.php
```

### 1.2 Frontend Admin Structure

```text
resources/
  views/
    backend/
      pages/
        testimonials/
          index.blade.php
          _results.blade.php
          _form.blade.php
          create.blade.php
          edit.blade.php
  js/
    admin/
      testimonials-dashboard.js
```

### 1.3 Route Pattern in Dashboard Group

Testimonial endpoints follow this naming:

1. dashboard.testimonials.index
2. dashboard.testimonials.table
3. dashboard.testimonials.create
4. dashboard.testimonials.store
5. dashboard.testimonials.edit
6. dashboard.testimonials.update
7. dashboard.testimonials.destroy
8. dashboard.testimonials.toggle-status
9. dashboard.testimonials.reorder

Permission mapping note:

1. dashboard.testimonials.table maps to testimonials.index in route-permission middleware.

## 2) Coding Pattern Used by Testimonial Module

### 2.1 Mandatory Layering

Controller -> Service -> Repository -> Model scopes

Rules:

1. Controller orchestrates request parsing and response formatting.
2. Service owns business operations and normalized write payloads.
3. Repository owns query and persistence logic.
4. Model owns scope-based dashboard list behavior.
5. FormRequest owns validation rules and config-bound limits.

### 2.2 Blade and JS Pattern

Rules:

1. No raw PHP blocks in Blade views.
2. index.blade.php contains filters and dashboard shell.
3. _results.blade.php contains table and pagination fragment.
4. _form.blade.php is reused by create and edit pages.
5. testimonials-dashboard.js handles realtime filtering, status toggle, and drag-drop reorder.

### 2.3 Request Validation Pattern

Use:

1. StoreTestimonialRequest
2. UpdateTestimonialRequest

Rules:

1. Rating max comes from config/testimonial.php.
2. is_published is boolean normalized from request.
3. sort_order remains integer and non-negative.

### 2.4 Repository and Query Pattern

Current repository behavior:

1. Dashboard listing via model scopes forDashboard/search/dashboardStatus/dashboardOrder.
2. Stats payload returns total/published/unpublished.
3. Reorder uses SQL CASE update on ordered IDs.
4. Toggle status returns refreshed model state.

### 2.5 Service Pattern

Service handles:

1. Listing payload composition.
2. create/update payload normalization.
3. next sort order resolution.
4. reorder normalization before repository call.

## 3) Dashboard Behavior Standards

### 3.1 Search and Filter

Required behavior:

1. Debounced search input.
2. Status dropdown filter.
3. Reset action.
4. AJAX partial table refresh.
5. URL query sync via history.replaceState.
6. AJAX pagination interception.

### 3.2 Sorting and Reorder

Required behavior:

1. Drag-drop row reorder on listing table.
2. POST JSON ordered IDs to reorder endpoint.
3. Optimistic sort-order label refresh in UI.
4. Fallback reload only on reorder failure.

### 3.3 Toggle Actions

Required behavior:

1. Status toggles are AJAX.
2. Checkbox rollback on error.
3. Success/error feedback via toast.

## 4) AI Agent Instruction Block (Mandatory)

### 4.1 Hard Rules

1. Follow this architecture exactly unless explicitly overridden.
2. Never use raw PHP blocks in Blade.
3. Never place query/business logic in controller or Blade.
4. Always keep validation in FormRequest classes.

### 4.2 Implementation Sequence

1. Model scopes.
2. Repository interface and implementation.
3. Service class.
4. FormRequests.
5. Controller endpoints.
6. Blade shell + partial + form partial.
7. Dashboard JS behavior.
8. Route and permission mapping.
9. Build and diagnostics validation.

### 4.3 Done Criteria

1. Realtime filter/search works.
2. AJAX pagination works.
3. AJAX status toggle works.
4. Drag-drop reorder persists.
5. No Blade raw PHP exists.
6. Build and diagnostics are clean.
