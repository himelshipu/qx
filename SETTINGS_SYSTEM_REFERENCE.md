# Settings System Reference (Admin Dashboard Canonical Pattern)

This document is the implementation blueprint for the Settings module in the admin dashboard.
Use it as the source of truth for architecture, naming, configuration, and dashboard behavior standards.

## 1) Settings File and Folder Structure (Reference First)

### 1.1 Backend Core Structure

```text
app/
  Http/
    Controllers/
      Backend/
        SettingsController.php
    Requests/
      Backend/
        Setting/
          UpdateBrandingSettingRequest.php
          UpdateEmailSettingRequest.php
          UpdatePlatformSettingRequest.php
          UpdateFooterSettingRequest.php
  Models/
    Setting.php
  Repositories/
    Contracts/
      SettingRepositoryInterface.php
    Eloquent/
      EloquentSettingRepository.php
  Services/
    Admin/
      SettingService.php

config/
  settings.php
```

### 1.2 Frontend Admin Structure

```text
resources/
  views/
    backend/
      pages/
        settings/
          index.blade.php
  js/
    admin/
      settings-dashboard.js
```

### 1.3 Route Pattern in Dashboard Group

Settings endpoints follow this naming:

1. dashboard.settings.index - View all settings
2. dashboard.settings.update-branding - Update branding section
3. dashboard.settings.update-email - Update email section
4. dashboard.settings.update-platform - Update platform section
5. dashboard.settings.update-footer - Update footer section
6. dashboard.settings.update-order - Update footer page order via AJAX
7. dashboard.settings.recovery.restore - Restore deleted records

## 2) Coding Pattern Used by Settings Module

### 2.1 Mandatory Layering

Controller -> Service -> Repository -> Model

Rules:

1. Controller orchestrates request parsing, form validation, and response formatting.
2. Service owns business operations: data composition, transformation, and section-specific logic.
3. Repository owns data persistence, caching, and file URL generation.
4. Model owns static helper methods (existing).
5. FormRequest owns validation rules and config-bound limits.

### 2.2 Blade and JS Pattern

Rules:

1. No raw PHP blocks in Blade views (@php...@endphp forbidden).
2. index.blade.php contains tab navigation and includes partial sections.
3. Each tab form is inline with separate route targets.
4. settings-dashboard.js handles file preview updates and sortable functionality.
5. All derived values pre-computed in controller, passed to view.

### 2.3 Request Validation Pattern

Use section-specific FormRequest classes:

1. UpdateBrandingSettingRequest
2. UpdateEmailSettingRequest
3. UpdatePlatformSettingRequest
4. UpdateFooterSettingRequest

Rules:

1. All limits come from config/settings.php keyed by section.
2. File upload validation rules normalize MIME types from config.
3. Enum validation uses config values (e.g., charge_type: percentage|fixed).

### 2.4 Repository and Query Pattern

Current repository behavior:

1. Cache all settings with 1-hour TTL.
2. get() retrieves a single key with default fallback.
3. set() updates or creates a setting and clears cache.
4. getSection() filters settings by prefix (e.g., "branding.").
5. updateSection() bulk updates all keys in a section.
6. fileUrl() handles Storage::url() resolution for file paths.

### 2.5 Service Pattern

Service handles:

1. Branding settings assembly (logo_light, logo_dark, favicon, site_name, tagline).
2. Email settings assembly (mailer, host, port, encryption, username, password, from_*).
3. Platform settings assembly (charge_type, charge_value).
4. Footer settings assembly (ordered page IDs).
5. Footer page reordering with JSON order normalization.
6. Update operations delegated to repository.

### 2.6 Configuration Pattern

Settings config file defines:

1. Per-section validation limits (max, min, allowed values).
2. File upload constraints (max file size in KB, allowed MIME types).
3. Dashboard behavior tuning (items_per_page, search_debounce, max_recovery_items).

All validation rules reference config keys, not hard-coded values.

## 3) Settings Sections and Behavior

### 3.1 Branding Tab

**Fields:**
- Site Name (required, max 255)
- Tagline (optional, max 255)
- Light Logo (image, max 6MB)
- Dark Logo (image, max 6MB)
- Favicon (image, max 2MB)

**Behavior:**
- File uploads store to `storage/app/public/settings/branding/`
- Preview regenerates on file selection via Alpine JS.
- Clear button removes file and resets preview.

### 3.2 Email Tab

**Fields:**
- Mailer (optional, max 50)
- SMTP Host (optional, max 255)
- SMTP Port (optional, integer 1-65535)
- Username (optional, max 255)
- Password (optional, max 255)
- Encryption (optional, in: tls,ssl)
- From Name (optional, max 255)
- From Address (optional, email)

**Behavior:**
- All fields optional for flexibility.
- Encryption validated against allowed config values.
- No immediate email test; validation only.

### 3.3 Platform Tab

**Fields:**
- Charge Type (required, in: percentage,fixed)
- Charge Value (required, numeric, min 0)

**Behavior:**
- Charge type controls whether value is % or fixed amount.
- Simple select + number input UI.

### 3.4 Footer Tab

**Fields:**
- Footer Pages (array of static_page IDs)
- Footer Pages Order (JSON array of sorted IDs)

**Behavior:**
- Drag-drop reorder using Sortable.js.
- Checkboxes to include/exclude pages.
- Only checked pages are persisted.
- Order persists via JSON hidden field.

### 3.5 Recovery Tab

**Read-only:**
- Lists soft-deleted records across all models with soft delete support.
- Grouped by type (Blog Post, Campaign, Influencer, etc.).
- Restore button triggers restore action with confirmation dialog.
- Displays deleted timestamp and identifying info.

## 4) Admin Dashboard Patterns

### 4.1 Tab Navigation

- Tab buttons control Alpine x-show() on tab divs.
- Current tab state synced to URL query string (?tab=branding).
- Handles history.replaceState() for clean back navigation.

### 4.2 Form Submission

- Each tab form POST to its specific route (e.g., dashboard.settings.update-branding).
- Redirects back to index with ?tab=X to keep tab context.
- Success/error messages via session flash.

### 4.3 File Uploads

- Logo uploads trigger file input change event.
- Alpine x-data computes preview via URL.createObjectURL().
- File name displays during upload before form submission.
- Clear button removes selection without submission.

## 5) AI Agent Instruction Block (Mandatory for Reuse)

### 5.1 Hard Rules

1. Replicate this Settings architecture exactly unless explicitly told otherwise.
2. Never place business/query logic in Blade.
3. Never use raw PHP blocks in Blade (@php forbidden).
4. Never bypass service and repository layers from controllers.
5. Never hard-code limits that belong in config/settings.php.

### 5.2 Implementation Sequence (Must Follow in Order)

1. Create config/settings.php with all section constraints.
2. Create SettingRepositoryInterface and EloquentSettingRepository.
3. Create SettingService with section-specific methods.
4. Create FormRequest classes for each section (UpdateBrandingSetting, etc.).
5. Create SettingsController with index + update methods per section.
6. Create Blade index view with tab navigation and section forms.
7. Create JS module for file preview and sortable interactions.
8. Add routes in dashboard group with correct naming.
9. Wire JS import in app entry.
10. Run build and validate all errors are clean.

### 5.3 Done Criteria for AI Agent

Do not mark task complete unless all checks pass:

1. Architecture parity with this reference is present.
2. Naming and folder structure parity is present.
3. All limits and validation values come from config/settings.php.
4. No Blade raw PHP exists in settings views.
5. Build and diagnostics are clean for touched files.
6. All routes are named dashboard.settings.*.
7. FormRequest classes validate against config values.
8. SettingService is injected into controller constructor.
9. SettingRepositoryInterface is bound in RepositoryServiceProvider.

## 6) File Uploads and Storage

### 6.1 Storage Location

All setting files stored under:
```
storage/app/public/settings/{section}/
```

Examples:
```
storage/app/public/settings/branding/logo-light.png
storage/app/public/settings/branding/favicon.ico
```

### 6.2 File URL Resolution

`SettingService::getBrandingSettings()` calls `repository->fileUrl()`:

- If path starts with http://, https://, or /, return as-is.
- Otherwise, call Storage::url() to resolve public URL.
- Return default value if setting not found.

### 6.3 File Cleanup (Future Enhancement)

Current implementation does not delete old files on update.
Consider adding cleanup service in future enhancement to remove orphaned files.

## 7) Testing Recommendations

### 7.1 Controller Tests

- POST to each dashboard.settings.update-* route with valid data.
- Verify redirects to index with correct tab query string.
- Verify FormRequest validation fails on invalid input.
- Verify FormRequest validates file size and MIME types.

### 7.2 Service Tests

- Call getBrandingSettings() and verify structure.
- Call updateBrandingSettings() and verify repository called.
- Call updateFooterSettings() with JSON order and verify normalization.

### 7.3 Repository Tests

- get() returns setting value or default.
- set() persists and clears cache.
- getSection() filters by prefix.
- fileUrl() resolves paths correctly.

### 7.4 Form Request Tests

- Validate all required fields.
- Validate file upload constraints.
- Validate enum values (charge_type, encryption).

## 8) Delivery Checklist for Settings Module

A module is not complete unless all items below are done.

- [ ] File and folder layout matches this document.
- [ ] Layering matches Controller -> Service -> Repository.
- [ ] No raw PHP blocks in Blade.
- [ ] All validation limits in config/settings.php.
- [ ] FormRequest classes for each section.
- [ ] SettingService with section-specific methods.
- [ ] EloquentSettingRepository with caching.
- [ ] SettingsController with index + update methods.
- [ ] Routes named dashboard.settings.*.
- [ ] Tab navigation and section forms in index.blade.php.
- [ ] JS module for file preview and sortable.
- [ ] RepositoryServiceProvider binds SettingRepositoryInterface.
- [ ] Build passes with no errors.
- [ ] No hard-coded values outside config/settings.php.

## 9) Future Enhancements (Optional)

1. Add settings search/filter for large settings collections.
2. Add audit logging for settings changes.
3. Add file cleanup service for orphaned uploads.
4. Add settings revert functionality to previous versions.
5. Add environment-specific settings override in .env.
6. Add settings import/export functionality.
