# Static Pages Feature - Quick Start Guide

## 🎯 Overview
The Static Pages feature provides a complete CRUD interface for managing static content pages (Privacy Policy, Terms of Use, About Us, Contact Us, etc.) in the admin dashboard with a rich text editor and SEO support.

## 📍 Location in Admin Dashboard
- **Menu Item:** Static Pages (in Content Management group)
- **Routes:** /dashboard/static-pages
  - List: `GET /dashboard/static-pages`
  - Create: `GET /dashboard/static-pages/create` | `POST /dashboard/static-pages`
  - View: `GET /dashboard/static-pages/{slug}`
  - Edit: `GET /dashboard/static-pages/{slug}/edit` | `PUT /dashboard/static-pages/{slug}`
  - Delete: `DELETE /dashboard/static-pages/{slug}`
  - Toggle: `POST /dashboard/static-pages/{slug}/toggle-status`

## �� Key Features

### Rich Text Editor
- **Editor:** TinyMCE 6 with full formatting toolbar
- **Plugins:** Lists, links, images, tables, code view, fullscreen
- **Height:** 400px (adjustable)
- **Dark Mode:** Auto-detects theme preference

### Slug-Based URLs
- **Format:** /dashboard/static-pages/privacy-policy
- **Auto-Generate:** Slug auto-generated from title if not provided
- **Manual Override:** Can be customized for custom URLs

### SEO Fields
- **Meta Description:** Up to 500 characters
- **Meta Keywords:** Comma-separated, up to 500 characters
- **Auto-Save:** Stored with each page

### Content Status
- **Published:** is_active = true (visible to users)
- **Draft:** is_active = false (hidden from users)
- **Toggle:** Can be toggled without full edit

## 👤 Permissions

### Required for Admin Access
All 8 permissions are granted to Admin role by default:
- `static-pages.index` - View all pages
- `static-pages.create` - Create new page
- `static-pages.store` - Save new page
- `static-pages.show` - View page details
- `static-pages.edit` - Edit page
- `static-pages.update` - Save changes
- `static-pages.destroy` - Delete page
- `static-pages.toggle-status` - Publish/unpublish

### For Custom Roles
To grant access to other roles, assign permissions via the Roles management interface.

## 📝 Pre-Seeded Pages

The following pages are automatically created:

1. **Privacy Policy**
   - Slug: `privacy-policy`
   - Status: Published
   - Content: Full privacy policy with sections

2. **Terms of Use**
   - Slug: `terms-of-use`
   - Status: Published
   - Content: Full terms and conditions

3. **About Us**
   - Slug: `about-us`
   - Status: Published
   - Content: Company mission, values, and team info

4. **Contact Us**
   - Slug: `contact-us`
   - Status: Published
   - Content: Contact methods and office hours

## 🚀 Usage Examples

### Create a New Page
1. Navigate to Admin Dashboard → Static Pages
2. Click "Create Page" button
3. Fill in:
   - Title: "Cookie Policy"
   - Slug: "cookie-policy" (auto-generated from title)
   - Content: Use TinyMCE editor to format content
   - Meta Description: "Our cookie policy and usage"
   - Meta Keywords: "cookies, privacy, tracking"
   - Publish: Check "Active" to publish immediately
4. Click "Save Page"

### Edit Existing Page
1. Navigate to Static Pages → All Pages
2. Click "Edit" button on any page
3. Modify content, title, or metadata
4. Click "Update Page"

### Publish/Unpublish
1. Navigate to Static Pages → All Pages
2. Click "Enable/Disable" toggle button
3. Status will update immediately without full edit

### View Page Details
1. Navigate to Static Pages → All Pages
2. Click "View" button to see full page content
3. From view page: Click "Edit" to modify or "Back to Pages" to return

## 🗄️ Database Schema

```sql
CREATE TABLE static_pages (
    id BIGINT PRIMARY KEY,
    title VARCHAR(255) UNIQUE,
    slug VARCHAR(255) UNIQUE,
    content LONGTEXT,
    meta_description VARCHAR(500) NULL,
    meta_keywords VARCHAR(500) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEXES: title, slug, is_active
);
```

## 🔧 Model & Controller

### Model: `App\Models\StaticPage`
```php
// Scopes
StaticPage::active()->get(); // Only published pages
StaticPage::bySlug('privacy-policy')->first(); // Find by slug
StaticPage::search('cookie')->get(); // Full-text search

// Accessors
$page->status_badge; // HTML badge (Published/Draft)
$page->content_summary; // First 150 chars (HTML stripped)
```

### Controller: `App\Http\Controllers\Backend\StaticPageController`
- All CRUD operations handled with validation
- Flash messages for success/error feedback
- Authorization checks on all operations

## 🎨 TinyMCE Editor Configuration

### Available Tools
- **Text Formatting:** Bold, Italic, Underline, Strikethrough
- **Alignment:** Left, Center, Right, Justify
- **Lists:** Bullets, Numbers, Outdent, Indent
- **Links:** Insert/edit hyperlinks with target options
- **Images:** Upload or link to images with alt text
- **Tables:** Create, edit tables with rows/columns
- **Code:** View and edit HTML source
- **Fullscreen:** Edit in fullscreen mode
- **Blocks:** Paragraph, headings, preformatted text

### Dark Mode Support
- Automatically detects user's theme preference from localStorage
- TinyMCE switches between oxide (light) and oxide-dark skins
- Content styles adjust for readability in both modes

## 📊 Validation Rules

### Title
- Required
- Unique per page (excluding current during update)
- Max 255 characters

### Slug
- Required
- Unique per page (excluding current during update)
- Max 255 characters
- Should be URL-friendly (lowercase, hyphens, no spaces)

### Content
- Required
- Can include HTML (TinyMCE formatted)

### Meta Description
- Optional
- Max 500 characters
- Recommended: 150-160 characters for optimal SEO

### Meta Keywords
- Optional
- Max 500 characters
- Comma-separated format

## 🔍 API Reference

### Tinker Commands
```php
// List all pages
php artisan tinker
> \App\Models\StaticPage::all();

// Find by slug
> \App\Models\StaticPage::bySlug('privacy-policy')->first();

// Search content
> \App\Models\StaticPage::search('cookie')->get();

// Count active pages
> \App\Models\StaticPage::active()->count();
```

### Artisan Commands
```bash
# Create a new static page via migration
php artisan migrate

# Reseed static pages (will not duplicate)
php artisan db:seed --class=StaticPageSeeder

# List all routes
php artisan route:list --name=static-pages
```

## ⚙️ Configuration

### Editor Height
Modify in `/resources/views/backend/pages/static-pages/form.blade.php`:
```javascript
tinymce.init({
    height: 400, // Change this value
    ...
});
```

### Pagination
Modify in `StaticPageController@index`:
```php
$pages = StaticPage::paginate(15); // Change 15 to desired per-page count
```

### Permissions
To create custom access level, assign individual permissions:
```php
$role->givePermissionTo('static-pages.index');
$role->givePermissionTo('static-pages.create');
// etc.
```

## 🐛 Troubleshooting

### TinyMCE Not Loading
- Check if CDN URL is accessible
- Verify JavaScript is not blocked
- Check browser console for errors
- Try clearing cache and reloading

### Pages Not Appearing
- Verify `is_active` is set to true
- Check permissions are assigned to role
- Ensure database migration was run

### Slug Already Exists
- Slugs must be unique
- Modify slug with a suffix (-2, -copy, etc.)
- Or auto-generate by leaving blank

### Content Not Saving
- Check validation errors (displayed in form)
- Ensure content is not too long (test with shorter text)
- Verify JavaScript is enabled
- Check browser console for errors

## 📚 Further Reading

- **File Structure:** See `STATIC_PAGES_QUICK_START.md`
- **Full Verification:** See `STATIC_PAGES_VERIFICATION_REPORT.md`
- **Routes:** `routes/web.php` lines 462-469
- **Permissions:** `database/seeders/PermissionSeeder.php` lines 319-326
- **Model:** `app/Models/StaticPage.php`
- **Controller:** `app/Http/Controllers/Backend/StaticPageController.php`

---

**Last Updated:** April 13, 2026  
**Feature Version:** 1.0.0  
**Status:** ✅ Production Ready
