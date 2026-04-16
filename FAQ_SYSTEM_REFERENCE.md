# FAQ System Reference (Admin Dashboard Canonical Pattern)

This document defines implementation rules for FAQ Sections and FAQ Items using Category-style architecture.

## 1) File and Folder Structure

### 1.1 Current Core Files

```text
app/
  Http/
    Controllers/
      Backend/
        FaqController.php
  Models/
    FaqSection.php
    FaqItem.php

resources/
  views/
    backend/
      pages/
        faqs/
          sections/
            index.blade.php
            create.blade.php
            edit.blade.php
          items/
            index.blade.php
            create.blade.php
            edit.blade.php
```

### 1.2 Target Standardized Structure

```text
app/
  Http/
    Controllers/
      Backend/
        FaqSectionController.php
        FaqItemController.php
    Requests/
      Backend/
        Faq/
          StoreFaqSectionRequest.php
          UpdateFaqSectionRequest.php
          StoreFaqItemRequest.php
          UpdateFaqItemRequest.php
  Models/
    FaqSection.php
    FaqItem.php
  Repositories/
    Contracts/
      FaqSectionRepositoryInterface.php
      FaqItemRepositoryInterface.php
    Eloquent/
      EloquentFaqSectionRepository.php
      EloquentFaqItemRepository.php
  Services/
    Admin/
      FaqSectionService.php
      FaqItemService.php

config/
  faq.php
```

## 2) Required Pattern

1. Split section and item responsibilities into dedicated controllers/services/repositories.
2. Add model scopes for dashboard list/search/status/order in both models.
3. Replace inline query and validation in controller with request/service/repository pattern.
4. Keep parent-child integrity checks inside service layer.

## 3) Required Dashboard Behavior

1. Realtime search/filter for sections and items pages.
2. AJAX partial results endpoint for each listing page.
3. AJAX status toggles for section and item.
4. AJAX pagination interception.
5. Optional reorder support where sort_order is user-managed.

## 4) AI Agent Instruction Block

1. Never keep combined monolithic CRUD logic inside one controller.
2. Never use raw PHP blocks in Blade templates.
3. Build reusable _results partials for sections and items.
4. Normalize route and permission slugs for section/item actions.
5. Validate with diagnostics and build before completion.
