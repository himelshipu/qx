# Static Pages Footer System - Implementation Guide

## Overview
A complete system to manage which static pages appear in the footer of the public website from the admin dashboard.

## How It Works

### Admin Dashboard
1. **Navigate to Settings**: Admin Dashboard → Static Pages → Footer Settings
2. **Select Pages**: Choose which static pages should appear in the footer
3. **Save**: Click "Save Settings"
4. **Result**: Selected pages immediately appear in footer links

### Public Website
1. **Footer Links**: Footer displays all selected static pages as links
2. **Click Link**: Users click on a footer link (Privacy Policy, Terms of Use, etc.)
3. **View Page**: User is taken to `/page/{slug}` URL showing the full page content
4. **Navigate Back**: Users can click "Back to Home" or use breadcrumb

## Database Structure

### Settings Table
Stores application-wide settings as JSON key-value pairs:
- `key`: Setting identifier (e.g., 'footer_pages')
- `value`: JSON array of page IDs

### Example
```
key: 'footer_pages'
value: [1, 2, 4]  // Privacy Policy, Terms of Use, About Us
```

## Routes

### Admin Routes
- `GET /dashboard/settings` - View/manage footer pages settings
- `POST /dashboard/settings` - Save footer pages configuration

### Public Routes
- `GET /page/{slug}` - Display a published static page

## Permission System

### Admin Permissions
- `settings.index` - View settings page
- `settings.update` - Update settings

These are automatically assigned to the Admin role.

## Files Created/Modified

### New Files Created
1. **Migration**: `database/migrations/2026_04_13_create_settings_table.php`
2. **Model**: `app/Models/Setting.php`
3. **Controller**: `app/Http/Controllers/Backend/SettingsController.php`
4. **Controller**: `app/Http/Controllers/PublicPageController.php`
5. **Views**: 
   - `resources/views/backend/pages/settings/index.blade.php`
   - `resources/views/frontend/pages/static.blade.php`

### Modified Files
1. **Routes**: `routes/web.php` - Added settings and public page routes
2. **Footer Component**: `resources/views/components/frontend/footer.blade.php` - Updated to display selected pages
3. **Permissions**: `database/seeders/PermissionSeeder.php` - Added settings permissions
4. **Role Permissions**: `database/seeders/RolePermissionSeeder.php` - Assigned permissions to admin
5. **Menu Helper**: `app/Helpers/MenuHelper.php` - Added menu item for settings

## Usage Workflow

### Step 1: Create Static Pages (Admin)
1. Go to Static Pages → Create Page
2. Fill in: Title, Slug, Content, Meta Description
3. Publish the page (toggle status)

### Step 2: Configure Footer (Admin)
1. Go to Static Pages → Footer Settings
2. Check the pages you want in footer
3. Click "Save Settings"

### Step 3: View in Footer (Public User)
1. Visit any page on the website
2. Scroll to footer
3. See the selected pages as clickable links
4. Click link to view full page content

## Features

✅ **Easy Configuration** - Simple checkbox interface
✅ **Real-time Updates** - Changes take effect immediately
✅ **Permission-based** - Only admins can manage settings
✅ **Draft/Published** - Only published pages can be selected
✅ **SEO-friendly** - Slugs used in URLs for clean SEO
✅ **Responsive** - Footer links work on all devices
✅ **Content Management** - Full HTML support via Quill editor

## Example Footer Output

```
© 2026 Rockies. All rights reserved.
  Privacy Policy | Terms of Use | About Us | Contact Us
```

Each link navigates to `/page/{slug}` showing the full content.

## Settings Model Usage

```php
// Get footer pages (returns array of IDs)
$footerPageIds = \App\Models\Setting::get('footer_pages', []);

// Set footer pages
\App\Models\Setting::set('footer_pages', [1, 2, 3]);

// In views - fetch and display
$pages = \App\Models\StaticPage::whereIn('id', $footerPageIds)
    ->where('is_active', true)
    ->get();
```

## Notes

- Only published pages are displayed in settings
- Only published pages are accessible on public domain
- Settings are cached and can be optimized with Laravel Cache
- Multiple admins can manage settings with proper permissions
