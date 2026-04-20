<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Category\StoreCategoryRequest;
use App\Http\Requests\Backend\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\Admin\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {}

    /**
     * Display a listing of categories with search and status filter.
     */
    public function index(Request $request): View
    {
        [$search, $status, $featured] = $this->resolveFilters($request);

        return view('backend.pages.categories.index', $this->categoryService->getListingPayload($search, $status, $featured));
    }

    /**
     * Return only dashboard category table HTML for faster filter updates.
     */
    public function table(Request $request): JsonResponse
    {
        [$search, $status, $featured] = $this->resolveFilters($request);
        $payload = $this->categoryService->getListingPayload($search, $status, $featured);

        $html = view('backend.pages.categories._results', [
            'categories' => $payload['categories'],
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('backend.pages.categories.create', [
            'maxFeatured' => (int) config('category.max_featured', 20),
        ]);
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categoryService->createCategory(
            $request->validated(),
            $request->boolean('is_active', true),
            $request->file('icon_file'),
            $request->file('image_file')
        );

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        return view('backend.pages.categories.edit', [
            'category' => $category,
            'maxFeatured' => (int) config('category.max_featured', 20),
        ]);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->categoryService->updateCategory(
            $category,
            $request->validated(),
            $request->boolean('is_active'),
            $request->file('icon_file'),
            $request->file('image_file')
        );

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category if it has no dependencies.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $result = $this->categoryService->deleteCategory($category);

        if (!$result['deleted']) {
            return redirect()
                ->route('dashboard.categories.index')
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', $result['message']);
    }

    /**
     * Toggle category status.
     */
    public function toggleStatus(Category $category): JsonResponse
    {
        $isActive = $this->categoryService->toggleStatus($category);

        return response()->json([
            'success'   => true,
            'message'   => 'Category status updated successfully.',
            'is_active' => $isActive
        ]);
    }

    /**
     * Resolve category dashboard filters from the request.
     *
     * @return array{0:string,1:string,2:string}
     */
    private function resolveFilters(Request $request): array
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');
        $featured = (string) $request->string('featured', 'all');

        return [$search, $status, $featured];
    }

}
