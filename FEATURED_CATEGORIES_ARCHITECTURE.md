# Featured Categories Architecture Diagram

## Data Flow

```
┌──────────────────────────────────────────────────────────────────┐
│                    Dashboard/Categories                          │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Filter Dropdown        Feature Position Button                 │
│  [Featured ▼]  ────────────► [★ Feature Position]               │
│                                      │                           │
│  Featured/Remove Filter              │                           │
│  [All ▼]                             │                           │
│                                      ▼                          │
│  Category Table                  ┌─────────────────┐            │
│   • Name                         │  Modal Window   │            │
│   • Status                       │                 │            │
│   • Actions                      │  Search Bar     │            │
│                                 │  Featured List  │            │
└──────────────────────────────────├─────────────────┤────────────┘
                                   │  Drag & Drop    │
                                   │  Priority Badges│
                                   │  Remove Buttons │
                                   └─────────────────┘
```

## Component Architecture

```
┌──────────────────────────────────────────────────────────────────┐
│                        Frontend Layer                             │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Blade Template                      JavaScript                 │
│  ├─ Filter Section                  ├─ Modal Management          │
│  ├─ Button Section                  ├─ Search Handler            │
│  ├─ Modal Component                 ├─ Drag-Drop Setup           │
│  └─ Pagination                      ├─ API Requests              │
│                                     └─ Toast Notifications       │
│                                                                  │
└──────────────────────┬───────────────────────────────────────────┘
                       │ AJAX Requests
                       ▼
┌──────────────────────────────────────────────────────────────────┐
│                       Controller Layer                            │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│  CategoryController              CategoryFeaturedController     │
│  ├─ index()                      ├─ getFeatured()               │
│  │  └─ featured parameter        ├─ addFeatured()               │
│  └─ Other actions               ├─ removeFeatured()            │
│                                 ├─ reorderFeatured()           │
│                                 └─ searchCategories()          │
│                                                                 │
└──────────────────────┬───────────────────────────────────────────┘
                       │ Dependency Injection
                       ▼
┌──────────────────────────────────────────────────────────────────┐
│                        Service Layer                             │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│  CategoryService                CategoryFeaturedService        │
│  ├─ getListingPayload()          ├─ getFeaturedCategories()    │
│  ├─ createCategory()             ├─ addFeatured()              │
│  ├─ updateCategory()             ├─ removeFeatured()           │
│  └─ deleteCategory()             ├─ updateOrder()              │
│                                  └─ updatePriorities()         │
│                                                                 │
└──────────────────────┬───────────────────────────────────────────┘
                       │ Data Access
                       ▼
┌──────────────────────────────────────────────────────────────────┐
│                     Repository Layer                             │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│  EloquentCategoryRepository                                     │
│  ├─ paginateForDashboard()                                      │
│  ├─ getFeaturedCategories()                                    │
│  ├─ searchCategories()                                         │
│  ├─ updateFeaturedOrder()                                      │
│  ├─ getFeaturedCount()                                         │
│  └─ getLowestPriorityFeatured()                                │
│                                                                 │
└──────────────────────┬───────────────────────────────────────────┘
                       │ ORM Query
                       ▼
┌──────────────────────────────────────────────────────────────────┐
│                      Database Layer                              │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│  categories table                                               │
│  ├─ id (primary key)                                            │
│  ├─ name                                                        │
│  ├─ slug                                                        │
│  ├─ is_featured (boolean)                                       │
│  ├─ featured_order (int, 1-10)                                  │
│  └─ ... other columns                                           │
│                                                                 │
└──────────────────────────────────────────────────────────────────┘
```

## Request Flow - Adding a Category to Featured

```
1. User Types in Search
   └─► Debounced (300ms) AJAX Request
       └─► GET /dashboard/categories-featured/search?q=product

2. Controller Receives Request
   └─► CategoryFeaturedController::searchCategories()
       └─► Validate query (min 2 chars)
           └─► Return JSON results

3. Frontend Renders Results
   └─► User clicks "Add" button on search result
       └─► POST /dashboard/categories-featured/add/5

4. Controller Handles Add
   └─► CategoryFeaturedController::addFeatured()
       └─► CategoryFeaturedService::addFeatured($category)
           ├─► Check if already featured
           ├─► Check current count (< 10)
           ├─► If == 10, remove lowest priority
           ├─► Add new category as top priority (1)
           └─► Recalculate all priorities
               └─► Update database

5. Return Updated Data
   └─► JSON Response with:
       ├─ success: true
       ├─ message: "Category added..."
       ├─ featured: [updated array]
       └─ removedCategory: [if auto-removed]

6. Frontend Updates UI
   └─► renderFeaturedList() with new data
       └─► Toast notification
           └─► Clear search input
```

## Drag & Drop Reordering Flow

```
1. User Drags Item
   ├─ SortableJS detects drag start
   ├─ Visual feedback (opacity change)
   └─ Maintains original array position

2. User Drops Item
   └─ New order determined
       └─ Array of IDs extracted: [3, 1, 5, 2, ...]
           └─ POST /dashboard/categories-featured/reorder
               └─ Body: {"order": [3, 1, 5, 2, ...]}

3. Controller Updates Order
   └─► CategoryFeaturedController::reorderFeatured()
       └─► CategoryFeaturedService::updateOrder($ids)
           ├─► Validate all IDs are featured
           └─► Update featured_order for each
               └─ ID[0] → featured_order = 1
               ─ ID[1] → featured_order = 2
               └─ ID[n] → featured_order = n+1

4. Database Persisted
   └─ All changes committed in single transaction

5. Return Updated List
   └─ Featured array with new priorities
       └─ Frontend renders with new order
           └─ All priority badges updated automatically
```

## Priority Management Logic

```
Current Featured Categories (before adding new):
┌─────────────────────────────────────┐
│ Priority │ Category ID │ Name        │
├──────────┼─────────────┼─────────────┤
│ 1        │ 5           │ Electronics │
│ 2        │ 3           │ Fashion     │
│ 3        │ 7           │ Home        │
│ 4        │ 2           │ Books       │
└─────────────────────────────────────┘

User adds: Category ID 9 (Sports)
─────────────────────────────────────

Service Logic:
├─ Check if already featured: NO
├─ Get current count: 4 (< 10)
│                        │
│                        └─► No need to remove
│
├─ Add new category with highest priority (4 + 1 = 5)? NO
│
├─ Instead: Make it top priority
│  └─ Call updatePriorities()
│     └─ Recalculate all sequentially

Result After Add:
┌─────────────────────────────────────┐
│ Priority │ Category ID │ Name        │
├──────────┼─────────────┼─────────────┤
│ 1        │ 9           │ Sports      │ <- NEW
│ 2        │ 5           │ Electronics │ <- Shifted down
│ 3        │ 3           │ Fashion     │ <- Shifted down
│ 4        │ 7           │ Home        │ <- Shifted down
│ 5        │ 2           │ Books       │ <- Shifted down
└─────────────────────────────────────┘
```

## State Management

```
Modal State:
├─ open: boolean
├─ featured: Array<{id, name, priority, ...}>
├─ count: number (current featured count)
├─ maxAllowed: number (10)
├─ loading: boolean
└─ searchResults: Array<Category>

On Modal Open:
├─ Fetch featured categories
├─ Load search handler
├─ Initialize SortableJS
└─ Render featured list

On Search:
├─ Debounce input (300ms)
├─ Minimum 2 characters
├─ Fetch results via AJAX
└─ Render search results

On Add:
├─ POST request to API
├─ Update featured array
├─ Update count
├─ Re-render list
├─ Clear search
└─ Show toast

On Remove:
├─ POST request to API
├─ Update featured array
├─ Update count
├─ Re-render list
└─ Show toast

On Reorder:
├─ Extract new order from SortableJS
├─ POST request with new order
├─ Update featured array with new priorities
├─ Re-render list
└─ Show toast
```

## API Response Examples

### Get Featured
```json
{
  "success": true,
  "data": {
    "featured": [
      {
        "id": 1,
        "name": "Electronics",
        "priority": 1,
        "icon_path": "categories/icons/...",
        "image_path": "categories/images/..."
      }
    ],
    "count": 7,
    "maxAllowed": 10
  }
}
```

### Add Featured
```json
{
  "success": true,
  "message": "Category added to featured list.",
  "removedCategory": null,
  "featured": [
    {
      "id": 5,
      "name": "Sports",
      "priority": 1,
      ...
    },
    ... (rest of featured categories)
  ]
}
```

### Add with Auto-Remove
```json
{
  "success": true,
  "message": "Category added to featured list.",
  "removedCategory": {
    "id": 42,
    "name": "Books"
  },
  "featured": [
    ...
  ]
}
```

## Error Handling

```
Validation Errors:
├─ Search < 2 chars: Return empty results
├─ Category not found: 404 response
├─ Category not active: Excluded from search
└─ Invalid order: 422 Unprocessable Entity

Frontend Error Handling:
├─ Catch fetch errors
├─ Show toast with error message
├─ Log to console
└─ Optionally reload data

Max Featured Errors:
├─ User is always informed
├─ Auto-removal is transparent
└─ Toast shows removed category name
```

---

This architecture ensures:
✅ Clean separation of concerns
✅ Easy testing and maintenance
✅ Scalability for future features
✅ Real-time user experience
✅ Data consistency
