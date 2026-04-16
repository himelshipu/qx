# Knowledge Base System Reference (Admin Dashboard Canonical Pattern)

This document defines the canonical implementation pattern for Knowledge Base admin module.

## 1) File and Folder Structure

### 1.1 Current Core Files

```text
app/
  Http/
    Controllers/
      Backend/
        KnowledgeBaseController.php
      Frontend/
        KnowledgeBaseController.php
  Models/
    KnowledgeBaseArticle.php

resources/
  views/
    backend/
      pages/
        knowledge-base/
          index.blade.php
          _form.blade.php
          create.blade.php
          edit.blade.php
```

### 1.2 Target Standardized Structure

```text
app/
  Http/
    Controllers/
      Backend/
        KnowledgeBaseController.php
    Requests/
      Backend/
        KnowledgeBase/
          StoreKnowledgeBaseArticleRequest.php
          UpdateKnowledgeBaseArticleRequest.php
  Models/
    KnowledgeBaseArticle.php
  Repositories/
    Contracts/
      KnowledgeBaseArticleRepositoryInterface.php
    Eloquent/
      EloquentKnowledgeBaseArticleRepository.php
  Services/
    Admin/
      KnowledgeBaseArticleService.php

config/
  knowledge-base.php
```

### 1.3 Frontend Admin Target Structure

```text
resources/
  views/
    backend/
      pages/
        knowledge-base/
          index.blade.php
          _results.blade.php
          _form.blade.php
          create.blade.php
          edit.blade.php
  js/
    admin/
      knowledge-base-dashboard.js
```

## 2) Required Coding Pattern

1. Controller -> Service -> Repository -> Model scopes.
2. Move validation from controller to FormRequest classes.
3. Keep slug generation uniqueness and publish date logic in service.
4. Add dashboard scopes: forDashboard, search, dashboardStatus, dashboardOrder.

## 3) Required Dashboard Behavior

1. Realtime search/filter (status, featured where needed).
2. AJAX table partial refresh endpoint.
3. AJAX pagination interception.
4. AJAX publish toggle with rollback-safe behavior.
5. Optional drag reorder if sort_order is manually managed by admins.

## 4) Data Rules

1. Keep read_time_minutes constraints in config and requests.
2. Keep published_at lifecycle aligned to is_published transitions.
3. Keep default ordering stable and explicit.

## 5) AI Agent Instruction Block

1. Implement architecture parity before adding enhancements.
2. No raw PHP blocks in Blade templates.
3. No direct query logic in controller actions.
4. Use table endpoint + _results partial for performant listing refresh.
5. Complete only after diagnostics/build pass and behavior parity checks.
