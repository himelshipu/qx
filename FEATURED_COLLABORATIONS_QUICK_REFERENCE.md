# Featured Collaborations - Quick Reference Guide

## 🎯 Access Points

### Dashboard

- **Route**: `/dashboard/featured-collaborations`
- **Menu**: Dashboard → MANAGEMENT → Featured Collaborations → All Collaborations
- **Create New**: `/dashboard/featured-collaborations/create`

### Frontend

- **Section**: "Trusted by 330,000+ Brands" on homepage
- **File**: `resources/views/components/frontend/partials/trusted-reviews.blade.php`
- **Display**: Only shows published collaborations in sort order

## 📂 New Files Created

### Models

```
app/Models/FeaturedCollaboration.php
  - Methods: getImageUrl(), getVideoUrl(), getThumbnailUrl()
  - Scope: published()
  - Fillable: brand_name, asset_type, image_path, video_path, thumbnail_path, sort_order, is_published
```

### Controllers

```
app/Http/Controllers/Backend/FeaturedCollaborationController.php
  - index()      - List collaborations
  - create()     - Show create form
  - store()      - Save new collaboration
  - edit()       - Show edit form
  - update()     - Update collaboration
  - destroy()    - Delete collaboration
  - togglePublish() - Toggle publish status
```

### Views

```
resources/views/backend/pages/featured-collaborations/
  ├── index.blade.php     - List all collaborations
  ├── create.blade.php    - Create form
  └── edit.blade.php      - Edit form
```

## 🔧 Updated Files

### Routes (`routes/web.php`)

- Added FeaturedCollaborationController import
- 7 new routes for CRUD operations

### Menu (`app/Helpers/MenuHelper.php`)

- Added "Featured Collaborations" to MANAGEMENT group
- Has 2 sub-items: All Collaborations, Add Collaboration

### Frontend (`resources/views/components/frontend/partials/trusted-reviews.blade.php`)

- Replaced hardcoded array with `FeaturedCollaboration::published()`
- Dynamic rendering of images/videos from database

## 🎨 Dashboard Design

All views feature:

- Gradient headers (Indigo → Purple)
- Dark mode support throughout
- Responsive layouts
- Hover effects and transitions
- Success messages
- Empty states
- Confirmation dialogs

## 📊 Typical Workflow

### Add a Collaboration

1. Admin logs in
2. Navigate to: Dashboard → Featured Collaborations
3. Click "Add Collaboration"
4. Fill form:
    - Brand Name: "Wealthsimple"
    - Asset Type: "Video"
    - Upload video file
    - Upload thumbnail (optional)
    - Set order (e.g., 1, 2, 3...)
    - Check "Publish"
5. Click "Create Collaboration"
6. Redirected to list view with success message
7. Collaboration appears on homepage

### Modify Display Order

1. Edit collaboration
2. Change "Display Order" number
3. Update
4. Homepage order refreshes automatically

### Hide/Show on Frontend

1. Go to collaborations list
2. Click status badge to toggle
3. Changes take effect immediately

## 💾 File Storage

```
storage/app/public/collaborations/
├── {id}-{filename}.extension (images)
├── videos/
│   └── {id}-{filename}.mp4 (videos)
└── thumbnails/
    └── {id}-{filename}.png (thumbnails)
```

## 🔐 Permissions

Currently accessible to:

- Admins
- Authenticated users with dashboard access

(Add specific role-based permissions as needed)

## 🐛 Troubleshooting

### Files Not Uploading

- Check `storage/app/public/` exists and is writable
- Verify file size limits are met
- Check disk space available

### Images Not Showing

- Ensure `php artisan storage:link` has been run
- Check file paths in database
- Verify file exists in storage

### Menu Item Not Showing

- Clear application cache: `php artisan cache:clear`
- Reload sidebar in dashboard
- Check MenuHelper.php for correct configuration

### Database Errors

- Ensure migration has been run: `php artisan migrate`
- Check `featured_collaborations` table exists

## 📱 Responsive Design

- **Mobile (< 640px)**: 2 columns
- **Tablet (640px - 768px)**: 3 columns
- **Desktop (768px - 1024px)**: 4 columns
- **Large (> 1024px)**: 5 columns

## 🎬 Video Handling

- Videos auto-pause when not hovered
- Plays from 0.1s (skip intro frame)
- Muted by default
- Play icon shows when paused
- Hover icon fades on playback

## 📸 Image Handling

- Lazy loaded
- Hover effect: Scale to 105%
- Smooth transitions (700ms)
- Dark background for contrast

---

**Last Updated**: March 17, 2026
**Status**: ✅ Fully Implemented and Ready for Use
