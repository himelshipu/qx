# Featured Collaborations - Technical Documentation

## 📊 Database Schema

### Table: `featured_collaborations`

| Column         | Type                  | Nullable | Default        | Description                       |
| -------------- | --------------------- | -------- | -------------- | --------------------------------- |
| id             | BIGINT UNSIGNED       | NO       | AUTO_INCREMENT | Primary key                       |
| page_id        | BIGINT UNSIGNED       | YES      | NULL           | Foreign key to pages table        |
| brand_name     | VARCHAR(255)          | YES      | NULL           | Name of the collaborating brand   |
| asset_type     | ENUM('image','video') | NO       | -              | Type of media: image or video     |
| image_path     | VARCHAR(500)          | YES      | NULL           | Path to image file in storage     |
| video_path     | VARCHAR(500)          | YES      | NULL           | Path to video file in storage     |
| thumbnail_path | VARCHAR(500)          | YES      | NULL           | Path to thumbnail file in storage |
| sort_order     | INT                   | NO       | 0              | Display order (ascending)         |
| is_published   | BOOLEAN               | NO       | true           | Visibility flag for frontend      |
| created_at     | TIMESTAMP             | YES      | NULL           | Record creation timestamp         |
| updated_at     | TIMESTAMP             | YES      | NULL           | Record update timestamp           |

## 🔌 API Routes (All require authentication)

### List Collaborations

```
GET /dashboard/featured-collaborations
Parameters: None
Response: Paginated list of collaborations (15 per page)
```

### Create Form

```
GET /dashboard/featured-collaborations/create
Parameters: None
Response: Create form view
```

### Store Collaboration

```
POST /dashboard/featured-collaborations
Parameters:
  - brand_name (required, string, max:255)
  - asset_type (required, enum: image or video)
  - image_path (optional, file, mimes: jpeg,png,webp,jpg, max: 5120KB)
  - video_path (optional, file, mimes: mp4,webm,mov, max: 102400KB)
  - thumbnail_path (optional, file, mimes: jpeg,png,webp,jpg, max: 2048KB)
  - sort_order (optional, integer, min: 0)
  - is_published (optional, boolean)
Response: Redirect to index with success message
```

### Edit Form

```
GET /dashboard/featured-collaborations/{featuredCollaboration}/edit
Parameters:
  - featuredCollaboration (required, integer - record ID)
Response: Edit form view with pre-filled data
```

### Update Collaboration

```
PATCH /dashboard/featured-collaborations/{featuredCollaboration}
Parameters: (Same as POST, all optional)
Response: Redirect to index with success message
```

### Delete Collaboration

```
DELETE /dashboard/featured-collaborations/{featuredCollaboration}
Parameters:
  - featuredCollaboration (required, integer - record ID)
Response: Redirect to index with success message
Note: Automatically deletes associated files from storage
```

### Toggle Publish Status

```
PATCH /dashboard/featured-collaborations/{featuredCollaboration}/toggle-publish
Parameters:
  - featuredCollaboration (required, integer - record ID)
Response: Redirect to index with success message
```

## 📦 Model Methods

### Public Methods

#### `getImageUrl(): ?string`

Returns full URL to image asset

```php
$url = $collaboration->getImageUrl();
// Returns: https://domain.com/storage/collaborations/12-image.jpg
```

#### `getVideoUrl(): ?string`

Returns full URL to video asset

```php
$url = $collaboration->getVideoUrl();
// Returns: https://domain.com/storage/collaborations/videos/12-video.mp4
```

#### `getThumbnailUrl(): ?string`

Returns full URL to thumbnail asset

```php
$url = $collaboration->getThumbnailUrl();
// Returns: https://domain.com/storage/collaborations/thumbnails/12-thumb.jpg
```

### Static Methods

#### `FeaturedCollaboration::published()`

Returns only published collaborations ordered by sort_order

```php
$collabs = FeaturedCollaboration::published();
// Returns: Collection of published records
```

## 🎯 Controller Methods Signature

```php
class FeaturedCollaborationController extends Controller
{
    public function index()
    public function create()
    public function store(Request $request)
    public function edit(FeaturedCollaboration $collaboration)
    public function update(Request $request, FeaturedCollaboration $collaboration)
    public function destroy(FeaturedCollaboration $collaboration)
    public function togglePublish(FeaturedCollaboration $collaboration)
}
```

## 🔐 File Upload Validation Rules

```
image_path:
  - nullable
  - image
  - mimes: jpeg, png, webp, jpg
  - max: 5120 Kb (5 MB)

video_path:
  - nullable
  - mimes: mp4, webm, mov
  - max: 102400 KB (100 MB)

thumbnail_path:
  - nullable
  - image
  - mimes: jpeg, png, webp, jpg
  - max: 2048 KB (2 MB)

brand_name:
  - required
  - string
  - max: 255

asset_type:
  - required
  - in: image, video

sort_order:
  - integer
  - min: 0

is_published:
  - boolean
```

## 📝 Usage Examples

### In Blade Templates

#### Display all published collaborations

```blade
@php
    $collaborations = \App\Models\FeaturedCollaboration::published();
@endphp

@foreach($collaborations as $collab)
    <div>
        @if($collab->asset_type === 'image')
            <img src="{{ $collab->getImageUrl() }}" alt="{{ $collab->brand_name }}">
        @else
            <video>
                <source src="{{ $collab->getVideoUrl() }}" type="video/mp4">
            </video>
        @endif
    </div>
@endforeach
```

#### Get single collaboration

```blade
@php
    $collaboration = \App\Models\FeaturedCollaboration::find(1);
@endphp

<h3>{{ $collaboration->brand_name }}</h3>
<img src="{{ $collaboration->getImageUrl() }}"
     alt="{{ $collaboration->brand_name }}">
```

### In Controllers

```php
use App\Models\FeaturedCollaboration;

// Get all published
$collabs = FeaturedCollaboration::published();

// Get with pagination
$collabs = FeaturedCollaboration::orderBy('sort_order')
    ->paginate(15);

// Get specific
$collab = FeaturedCollaboration::find(1);

// Create
FeaturedCollaboration::create([
    'brand_name' => 'Brand Name',
    'asset_type' => 'image',
    'image_path' => 'path/to/image.jpg',
    'sort_order' => 1,
    'is_published' => true
]);

// Update
$collab->update(['is_published' => false]);

// Delete
$collab->delete();
```

## 🔄 File Management

### Storage Location

```
storage/app/public/collaborations/
├── images/          (Images for collaborations)
├── videos/          (Video files)
└── thumbnails/      (Thumbnail images)
```

### Automatic Cleanup

- When updating: Old files are deleted before uploading new ones
- When deleting: All associated files are removed from storage
- Orphaned files: Check storage folder periodically

### Accessing Files

```php
// Direct URL
$url = asset('storage/collaborations/image.jpg');

// Via model method (recommended)
$url = $collaboration->getImageUrl();
```

## ⚙️ Configuration

### Environment Requirements

```
APP_ENV=production (or development)
FILESYSTEM_DISK=public
```

### Required Directories

```
storage/app/public/        (must exist and be writable)
storage/app/public/collaborations/
storage/app/public/collaborations/videos/
storage/app/public/collaborations/thumbnails/
```

### Symbolic Link

Ensure storage symbolic link is created:

```bash
php artisan storage:link
```

## 🔍 Query Examples

### Get collaborations by asset type

```php
$images = FeaturedCollaboration::where('asset_type', 'image')
    ->where('is_published', true)
    ->orderBy('sort_order')
    ->get();

$videos = FeaturedCollaboration::where('asset_type', 'video')
    ->where('is_published', true)
    ->orderBy('sort_order')
    ->get();
```

### Get recently created

```php
$recent = FeaturedCollaboration::latest('created_at')
    ->where('is_published', true)
    ->limit(5)
    ->get();
```

### Get by sort order range

```php
$featured = FeaturedCollaboration::whereBetween('sort_order', [1, 5])
    ->where('is_published', true)
    ->get();
```

### Count by type

```php
$imageCount = FeaturedCollaboration::where('asset_type', 'image')->count();
$videoCount = FeaturedCollaboration::where('asset_type', 'video')->count();
```

## 🧪 Testing Routes

### Using cURL

```bash
# List collaborations
curl -H "Authorization: Bearer {token}" \
  https://domain.com/dashboard/featured-collaborations

# Create collaboration
curl -X POST \
  -H "Authorization: Bearer {token}" \
  -F "brand_name=Wealthsimple" \
  -F "asset_type=image" \
  -F "image_path=@/path/to/image.jpg" \
  -F "sort_order=1" \
  -F "is_published=1" \
  https://domain.com/dashboard/featured-collaborations
```

---

**Version**: 1.0
**Last Updated**: March 17, 2026
**Status**: Production Ready
