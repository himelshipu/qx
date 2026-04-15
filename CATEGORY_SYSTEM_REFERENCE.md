# Category System Reference

This file is the single reference for reusing the category backend pattern in other dashboard modules.

## Architecture

- Controller: thin HTTP layer, reads request filters, delegates to service.
- Service: business rules, asset handling, slug generation, delete guards.
- Repository: all database access, query composition, stats, pagination, and cache invalidation.
- Model: reusable query scopes and relationship definitions.

## Folder Structure

- `app/Http/Controllers/Backend/CategoryController.php`
- `app/Services/Admin/CategoryService.php`
- `app/Repositories/Contracts/CategoryRepositoryInterface.php`
- `app/Repositories/Eloquent/EloquentCategoryRepository.php`
- `app/Models/Category.php`

Use the same layout for future dashboard systems:

- `XController`
- `XService`
- `XRepositoryInterface`
- `EloquentXRepository`
- `X` model with scopes

## Query Pattern

The category repository now follows a consistent high-performance pattern:

- Use model scopes for reusable filters.
- Select only columns needed by the dashboard list.
- Use `withCount()` only for counters shown in the UI.
- Keep filter logic in the model scopes, not in controllers.
- Keep pagination query construction inside the repository.
- Cache expensive summary stats briefly and invalidate on writes.

### Current Scopes on `Category`

- `active()`
- `inactive()`
- `featured()`
- `nonFeatured()`
- `forDashboard()`
- `search($term)`
- `dashboardStatus($status)`
- `dashboardFeatured($featured)`
- `dashboardOrder()`

## Performance Notes

- Dashboard listing uses a narrow select set instead of loading full rows.
- Search is centralized in a model scope so other systems can reuse the same pattern.
- Stats are cached for a short period to reduce repeated count queries.
- Cache is cleared on create, update, delete, toggle status, and featured order changes.
- Featured category queries only select the columns needed for the modal.

## Reusable Implementation Rules

1. Put query filters in model scopes.
2. Keep repositories responsible for query assembly and pagination.
3. Keep services responsible for business rules and state changes.
4. Keep controllers thin and request-focused.
5. Use small, explicit selects for dashboard tables.
6. Cache only expensive aggregate reads, and invalidate them on writes.
7. Prefer short, composable methods over large controller methods.

## Category Use Cases Covered

- Listing categories with search, status, and featured filters.
- Create, update, and delete flows with asset cleanup.
- Featured category modal with ordering and max limit.
- Reusable dashboard pagination behavior.
- Reusable summary stats and relation counts.

## If Reusing For Another Module

Copy the structure and swap the domain model:

- Replace `Category` with the new model.
- Replace the repository interface and Eloquent implementation names.
- Add equivalent model scopes for the new domain filters.
- Keep the controller thin and the service as the business-rule boundary.

## Notes

- This reference intentionally stays in one file.
- Keep future module docs consolidated the same way.
