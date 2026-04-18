# Support Ticket System Reference (Admin Dashboard Canonical Pattern)

This document defines the implementation blueprint for the admin Support Tickets module.
Use it as the canonical reference for file/folder structure, naming, layering, filtering, table refresh, and ticket update behavior.

## 1) File and Folder Structure

### 1.1 Backend Core Structure

```text
app/
  Http/
    Controllers/
      Backend/
        SupportTicketController.php
    Requests/
      Backend/
        SupportTicket/
          BulkUpdateSupportTicketRequest.php
          UpdateSupportTicketRequest.php
  Models/
    SupportTicket.php
    SupportCategory.php
  Repositories/
    Contracts/
      SupportTicketRepositoryInterface.php
    Eloquent/
      EloquentSupportTicketRepository.php
  Services/
    Admin/
      SupportTicketService.php

config/
  support-ticket.php
```

### 1.2 Frontend Admin Structure

```text
resources/
  views/
    backend/
      pages/
        support-tickets/
          index.blade.php
          _results.blade.php
          show.blade.php
  js/
    admin/
      support-tickets-dashboard.js
```

### 1.3 Route Pattern in Dashboard Group

Support ticket dashboard endpoints follow this naming:

1. dashboard.support-tickets.index
2. dashboard.support-tickets.table
3. dashboard.support-tickets.show
4. dashboard.support-tickets.update
5. dashboard.support-tickets.destroy
6. dashboard.support-tickets.bulk-update

## 2) Coding Pattern Used by Support Ticket Module

### 2.1 Mandatory Layering

Always follow this flow:

Controller -> Service -> Repository -> Model scopes

Rules:

1. Controller is orchestration only.
2. Service owns business rules and workflow decisions.
3. Repository owns query composition and persistence.
4. Model owns reusable scopes and presentation helpers.
5. FormRequest owns validation.

### 2.2 Blade and JS Pattern

Rules:

1. No raw PHP blocks in Blade views.
2. Derived classes and labels should be supplied by the model or service.
3. Keep page shell in index.blade.php and rows/table markup in _results.blade.php.
4. Keep table refresh/search behavior in a dedicated JS module.
5. Support ticket dashboard should update without a full page submit.

### 2.3 Request Validation Pattern

Use module-specific FormRequest classes:

1. UpdateSupportTicketRequest
2. BulkUpdateSupportTicketRequest

Rules:

1. Validation rules must reflect the dashboard action being performed.
2. Keep allowed statuses and priorities aligned with the module schema.
3. Use nullable assignment fields where unassigned is valid.

### 2.4 Repository and Query Pattern

Required behavior:

1. List query uses dashboard-specific scopes.
2. Search is handled by a reusable model scope.
3. Filter methods are scope-driven for status, category, and priority.
4. Ordering is centralized in a model scope.
5. Stats are computed in the repository from canonical counts.

### 2.5 Service Pattern

Services must handle:

1. Payload assembly for index and table views.
2. Status/priority update orchestration.
3. Resolved/closed timestamp behavior.
4. Bulk update normalization.
5. Delete orchestration.

### 2.6 Data and Schema Pattern

Use config for shared module constants:

1. support-ticket.pagination
2. support-ticket.new_status
3. support-ticket.admin_roles

## 3) Support Ticket Behavior to Mirror in Other Modules

### 3.1 Search and Filter

Support ticket pattern:

1. Debounced text search.
2. Select filters for status, category, and priority.
3. Reset link to clear filters.
4. AJAX table refresh via dedicated table endpoint.
5. URL query sync via history.replaceState.
6. AJAX pagination interception without full reload.

### 3.2 Update Flow

Rules:

1. Status changes can set resolved_at or closed_at.
2. Assigned user can be cleared by passing an empty value.
3. Updates should go through FormRequest + service + repository.
4. Bulk update should validate ids and apply the same business rules.

### 3.3 Presentation Rules

1. Status and priority labels should be derived from model helpers.
2. Ticket rows should not embed raw PHP matching logic.
3. Category, requester, and assigned user relations should be eager loaded.

## 4) Naming Pattern Standards

### 4.1 Files and Classes

Use singular domain in class names:

1. SupportTicketController
2. SupportTicketService
3. SupportTicketRepositoryInterface
4. EloquentSupportTicketRepository
5. UpdateSupportTicketRequest
6. BulkUpdateSupportTicketRequest

Blade folder uses plural resource name:

1. resources/views/backend/pages/support-tickets

JS file naming:

1. support-tickets-dashboard.js

### 4.2 Routes and Permissions

Routes:

1. Prefix under dashboard group.
2. Route names follow dashboard.support-tickets.action.
3. Table endpoint is separate from the main index route.

Permissions:

1. Keep action-based slugs aligned with route intent.
2. Add or adjust permission slugs before shipping any new actions.

## 5) Done Criteria for Support Ticket Module

Do not mark the module complete unless all checks pass:

1. Folder and naming structure match this document.
2. Controller -> Service -> Repository -> Model scope layering is present.
3. No raw PHP blocks exist in ticket views.
4. Search/filter behavior is realtime and no-submit-button.
5. AJAX table endpoint exists and returns the rows partial.
6. Pagination works without full page reload.
7. Status and assignment update flow remains intact.
8. JS module is imported in the main app entry.
9. Build and diagnostics are clean for touched files.

## 6) AI Agent Instruction Block

1. Never put filtering logic only in Blade.
2. Never use raw PHP blocks in support ticket views.
3. Never bypass service and repository layers from controllers.
4. Never hard-code pagination or status constants in multiple layers.
5. Always validate with diagnostics and build before completion.
