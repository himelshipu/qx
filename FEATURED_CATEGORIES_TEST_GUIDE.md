# Featured Categories Dashboard - Test Guide

## Quick Start

1. **Access the Dashboard**
   - Navigate to: `http://qx.local/dashboard/categories`

2. **Test Filter Dropdown**
   - Look for the new filter dropdown before the Reset button
   - Options: "All", "Featured", "Non-Featured"
   - Test each option and verify filtering works correctly

3. **Test Feature Position Button**
   - Look for the new "Feature Position" button next to "New Category"
   - Click to open the modal

## Feature Testing

### 1. Filter Dropdown
- **Location**: Before the "Reset" button
- **Options**: 
  - All (default) - Shows all categories
  - Featured - Shows only categories with `is_featured = true`
  - Non-Featured - Shows only categories with `is_featured = false`
- **Test**: Change filter and verify URL updates with `?featured=featured` or `?featured=non-featured`

### 2. Feature Position Modal
- **Trigger**: Click "Feature Position" button
- **Initial Load**: Displays top 10 featured categories (if available) sorted by `featured_order` ASC
- **Layout**: Modal with search bar, featured list, and priority badges

### 3. Search Functionality
- **Location**: Inside modal
- **Behavior**: 
  - Type category name (min 2 chars)
  - Results appear dynamically below search box
  - Shows if category is already featured
  - Shows "Add" or "Featured" button depending on state
- **Test**: Search for "Product", "Service", etc.

### 4. Drag & Drop Reordering
- **Trigger**: Featured categories in modal
- **Behavior**:
  - Cursor changes to grab cursor on hover
  - Drag items to reorder
  - Priority numbers update automatically (1=top, 10=bottom)
  - Changes sync to backend
- **Test**: 
  - Drag first item to bottom
  - Verify priority updates to 10
  - Refresh page to confirm persistence

### 5. Feature Button
- **Location**: Search results in modal
- **Behavior**:
  - Click "Add" button on any search result
  - If < 10 categories featured, adds to top priority
  - If >= 10 featured, removes lowest priority and adds new one
  - Updates list immediately
  - Shows toast notification about auto-removed category
- **Test**:
  - Search for a category
  - Click "Add" button
  - Verify it appears in featured list with priority 1
  - Other items shift down

### 6. Remove Button
- **Location**: Next to each priority badge in featured list
- **Behavior**:
  - Clicking X removes category from featured
  - Recalculates priorities (renumbers 1-N)
  - Updates immediately
  - Shows success toast
- **Test**:
  - Click remove (X) on any featured category
  - Verify it's removed from list
  - Verify other priorities update

### 7. Max 10 Featured Categories
- **Behavior**:
  - Can never exceed 10 featured categories
  - When adding 11th, the 10th (lowest) auto-removes
  - Notification shows which was removed
- **Test**:
  - Create/feature 10 categories
  - Try to feature an 11th
  - Verify that the 10th auto-removes
  - Notification shows the removed category name

### 8. Backend Sync
- **Real-time Updates**: All changes save immediately
- **Persistence**: Refresh page and verify changes persist
- **Database Verification**:
  ```sql
  SELECT id, name, is_featured, featured_order FROM categories 
  WHERE is_featured = true ORDER BY featured_order;
  ```

## API Endpoints for Manual Testing

### Get Featured Categories
```
GET /dashboard/categories-featured
Response: Includes featured array, count, and maxAllowed
```

### Add to Featured
```
POST /dashboard/categories-featured/add/5
Headers: X-CSRF-TOKEN, Content-Type: application/json
Response: Success message, featured array, possibly removedCategory
```

### Remove from Featured
```
POST /dashboard/categories-featured/remove/5
Headers: X-CSRF-TOKEN, Content-Type: application/json
Response: Success message, updated featured array
```

### Reorder Featured
```
POST /dashboard/categories-featured/reorder
Headers: X-CSRF-TOKEN, Content-Type: application/json
Body: {"order": [5, 3, 1, 7, 2, ...]}
Response: Success message, updated featured array with new priorities
```

### Search Categories
```
GET /dashboard/categories-featured/search?q=product
Response: Results array with id, name, slug, is_featured, priority, paths
```

## Database Schema Verification

Verify these columns exist in `categories` table:
- `is_featured` (boolean, default: false)
- `featured_order` (integer, nullable)

Run migration if needed:
```bash
php artisan migrate
```

## UI Elements Checklist

- [ ] Featured filter dropdown before Reset button
- [ ] "Feature Position" button next to "New Category"
- [ ] Modal opens when clicking button
- [ ] Search bar in modal with live filtering
- [ ] Featured list shows with priority badges (1-10)
- [ ] Drag handles visible on featured items
- [ ] Drag-drop works smoothly
- [ ] Remove buttons (X) working on each item
- [ ] Toast notifications showing for all actions
- [ ] "Add" buttons on search results
- [ ] Priority numbers update automatically

## Common Issues & Solutions

### Modal doesn't open
- Check browser console for errors
- Verify SortableJS CDN is loading
- Check CSRF token is present

### Drag-drop not working
- SortableJS library needs to load
- Check CDN connectivity
- Verify JavaScript console for errors

### Search not returning results
- Minimum 2 characters required
- Only searches active categories
- Check category names match search query

### Priority numbers not updating
- Try refreshing the page
- Check server logs for errors
- Verify database has proper featured_order values

## Performance Testing

1. **Large Lists**: Test with 50+ categories
2. **Search Performance**: Search should be instant
3. **Drag Performance**: Smooth drag-drop with 10 items
4. **Network**: Test with slow network (DevTools throttling)

## Browser Compatibility

- Chrome/Edge: ✓ Full support
- Firefox: ✓ Full support
- Safari: ✓ Full support
- Mobile Safari: Check touch drag-drop

## Notes

- Maximum featured categories is hardcoded to 10 (can be changed in `CategoryFeaturedService::MAX_FEATURED`)
- Featured order priority 1 = highest (top of list)
- Categories must be active (is_active = true) to be featured
- All changes are persisted to database immediately (no save button)
