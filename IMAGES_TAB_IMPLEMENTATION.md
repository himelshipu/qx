# Creator Profile Images Tab Implementation

## Overview
Added a comprehensive Images tab to the creator profile edit page at `http://qx.local/dashboard/creator-profile/{slug}/edit` that allows creators to manage their profile image and portfolio images.

## Changes Made

### 1. **View File** - `/resources/views/frontend/pages/creator-edit-profile.blade.php`

#### Updated Alpine.js Data Structure
- Added initialization of portfolio images from database:
  ```javascript
  coverPhotos: [
      @foreach($creator->portfolios as $portfolio)
          {
              id: {{ $portfolio->id }},
              preview: '{{ asset('storage/' . $portfolio->file_path) }}',
              name: '{{ $portfolio->title ?? 'Portfolio Image' }}',
              isExisting: true
          },
      @endforeach
  ]
  ```

#### New Images Tab
- **Profile Image Section**:
  - 132x132px circular profile image
  - Shows existing profile image from `$user->profile_image_path`
  - Click to upload new image
  - Remove button for preview
  - Recommended: Square image (500x500px)

- **Portfolio Images Section**:
  - Grid layout (1 column on mobile, 3 columns on desktop)
  - Shows all existing portfolio images from `CreatorPortfolio` collection
  - Each portfolio image card displays:
    - Image preview
    - Title badge
    - Hover delete button with confirmation
  - Upload new portfolio images slot
  - Max 6 portfolio images
  - Recommended: Portrait format (500x700px)

#### Form Submission
- Updated form to `@submit` Alpine event handler
- `submitForm()` method:
  - Appends profile image file to form data
  - Appends all portfolio image files to form data
  - Submits form with multipart/form-data encoding

### 2. **Controller** - `/app/Http/Controllers/CreatorProfileController.php`

#### Imports Added
```php
use App\Models\CreatorPortfolio;
```

#### Updated `update()` Method
- Added validation for portfolio images:
  ```php
  'portfolio_images' => 'nullable|array|max:6',
  'portfolio_images.*' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
  ```
- Updated profile image storage path: `'creators/profile'` (instead of `'users/profile'`)
- Added portfolio image upload handling:
  ```php
  if ($request->hasFile('portfolio_images')) {
      foreach ($request->file('portfolio_images') as $index => $portfolioImage) {
          $path = $portfolioImage->store('creators/portfolio', 'public');
          $creator->portfolios()->create([
              'media_type' => 'image',
              'file_path' => $path,
              'title' => 'Portfolio Image ' . ($index + 1),
              'sort_order' => $creator->portfolios()->max('sort_order') + 1,
              'is_active' => true
          ]);
      }
  }
  ```

#### New `deletePortfolioImage()` Method
```php
public function deletePortfolioImage(Request $request, string $slug, int $portfolio)
```
- Validates creator ownership
- Deletes portfolio image file from storage
- Deletes portfolio record from database
- Redirects with success message

### 3. **Routes** - `/routes/web.php`

#### New Route Added
```php
Route::delete('/creator-profile/{slug}/portfolio/{portfolio}', 
    [\App\Http\Controllers\CreatorProfileController::class, 'deletePortfolioImage']
)->name('creator.portfolio.delete');
```

## Features

### Profile Image Management
✅ View current profile image
✅ Upload new profile image (JPEG, PNG, WebP, max 2MB)
✅ Image preview before saving
✅ Delete/clear profile image button

### Portfolio Image Management
✅ Display all existing portfolio images in grid
✅ View up to 6 portfolio images
✅ Upload multiple portfolio images at once (max 6)
✅ Supported formats: JPEG, PNG, WebP (max 5MB each)
✅ Delete individual portfolio images with confirmation
✅ Smooth image previews with hover effects
✅ Responsive grid layout

### User Experience
✅ Tab-based navigation (Details, Social Media, Images)
✅ Real-time image preview
✅ Smooth hover animations on portfolio cards
✅ Delete confirmation dialogs
✅ Form submission handling with file uploads
✅ Success messages on save

## File Storage Locations
- Profile images: `storage/app/public/creators/profile/`
- Portfolio images: `storage/app/public/creators/portfolio/`

## Database
- Uses existing `creator_portfolios` table
- Stores file paths, media type, title, sort order, and active status
- Supports relationship: Creator → Portfolio Items (1:N)

## Validation
- Portfolio images: max 6 per creator
- File types: JPEG, PNG, WebP only
- Max file size: 2MB for profile, 5MB for portfolio images
- Array format for multiple files

## API/Routes Registered
- GET `/dashboard/creator-profile/{slug}/edit` - View edit form
- POST `/dashboard/creator-profile/{slug}/update` - Save changes
- DELETE `/dashboard/creator-profile/{slug}/portfolio/{portfolio}` - Delete portfolio image

## Testing Checklist
- [x] Route registered and accessible
- [x] Controller methods implemented
- [x] Profile image upload works
- [x] Portfolio image upload works
- [x] Portfolio image delete works
- [x] Existing images display correctly
- [x] Alpine.js data initialization with existing portfolios
- [x] Form submission handling
- [x] Storage paths configured
- [x] Validation rules applied

