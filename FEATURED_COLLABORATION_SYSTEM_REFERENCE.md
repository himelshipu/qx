# Featured Collaboration System Reference (Admin Dashboard Canonical Pattern)

This document defines how Featured Collaborations should be standardized to Category-level architecture and UX behavior.

## 1) File and Folder Structure

### 1.1 Backend Core Structure (Current)

```text
app/
  Http/
    Controllers/
      Backend/
        FeaturedCollaborationController.php
  Models/
    FeaturedCollaboration.php
```

### 1.2 Backend Target Structure (Required)

```text
app/
  Http/
    Controllers/
      Backend/
        FeaturedCollaborationController.php
    Requests/
      Backend/
        FeaturedCollaboration/
          StoreFeaturedCollaborationRequest.php
          UpdateFeaturedCollaborationRequest.php
  Models/
    FeaturedCollaboration.php
  Repositories/
    Contracts/
      FeaturedCollaborationRepositoryInterface.php
    Eloquent/
      EloquentFeaturedCollaborationRepository.php
  Services/
    Admin/
      FeaturedCollaborationService.php

config/
  featured-collaboration.php
```

### 1.3 Frontend Admin Target Structure

```text
resources/
  views/
    backend/
      pages/
        featured-collaborations/
          index.blade.php
          _results.blade.php
          _form.blade.php
          create.blade.php
          edit.blade.php
  js/
    admin/
      featured-collaborations-dashboard.js
      featured-collaboration-form.js
```

## 2) Required Coding Pattern

1. Controller -> Service -> Repository -> Model scopes.
2. No raw PHP blocks in Blade.
3. No direct media lifecycle logic in controller; move to service.
4. Validation must be in FormRequest classes.
5. Keep media constraints and allowed MIME types in config.

## 3) Required Dashboard Behavior

1. Realtime search/filter with AJAX partial refresh.
2. AJAX pagination interception.
3. AJAX publish toggle with rollback on failure.
4. Drag-drop reorder endpoint and sortable rows.
5. URL query sync on filter changes.

## 4) Data and Media Rules

1. asset_type drives image/video/thumbnail constraints.
2. All file writes and deletes handled in service.
3. Use storage public disk and cleanup old files on replacements.
4. Reorder writes must be batch SQL update style.

## 5) AI Agent Instruction Block

1. Implement architecture parity before UI polish.
2. Build _results partial and route table endpoint for AJAX listing.
3. Use data-* route templates in Blade root and JS event delegation.
4. Add repository binding and permission slug coverage for any new route names.
5. Do not mark complete until build and diagnostics are clean.
