<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Brand\StoreBrandRequest;
use App\Http\Requests\Backend\Brand\UpdateBrandRequest;
use App\Models\Brand;
use App\Services\Admin\BrandService;
use App\Traits\Sortable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    use Sortable;

    public function __construct(
        private readonly BrandService $brandService
    ) {}

    /**
     * Display a listing of brands with search and status filter.
     */
    public function index(Request $request): View
    {
        [$search, $status] = $this->resolveFilters($request);

        return view('backend.pages.brands.index', $this->brandService->getListingPayload($search, $status));
    }

    /**
     * Return only dashboard brand table HTML for faster filter updates.
     */
    public function table(Request $request): JsonResponse
    {
        [$search, $status] = $this->resolveFilters($request);
        $payload = $this->brandService->getListingPayload($search, $status);

        $html = view('backend.pages.brands._results', [
            'brands' => $payload['brands'],
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create(): View
    {
        return view('backend.pages.brands.create', $this->brandService->getFormPayload());
    }

    /**
     * Store a newly created brand.
     */
    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $this->brandService->createBrand(
            $request->validated(),
            $request->boolean('is_active', true),
            $request->file('profile_image_file'),
            $request->file('cover_image_file')
        );

        return redirect()
            ->route('dashboard.brands.index')
            ->with('success', 'Brand created successfully.');
    }

    /**
     * Display the specified brand details.
     */
    public function view(Brand $brand): View
    {
        return view('backend.pages.brands.view', $this->brandService->getDetailPayload($brand));
    }

    /**
     * Show the form for editing the specified brand.
     */
    public function edit(Brand $brand): View
    {
        return view('backend.pages.brands.edit', $this->brandService->getFormPayload($brand->load('user')));
    }

    /**
     * Update the specified brand.
     */
    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        $this->brandService->updateBrand(
            $brand,
            $request->validated(),
            $request->boolean('is_active'),
            $request->file('profile_image_file'),
            $request->file('cover_image_file')
        );

        return redirect()
            ->route('dashboard.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    /**
     * Remove the specified brand if no dependencies exist.
     */
    public function destroy(Brand $brand): RedirectResponse
    {
        $result = $this->brandService->deleteBrand($brand);

        return redirect()
            ->route('dashboard.brands.index')
            ->with($result['deleted'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Toggle brand status.
     */
    public function toggleStatus(Brand $brand): JsonResponse
    {
        $isActive = $this->brandService->toggleStatus($brand);

        return response()->json([
            'success'   => true,
            'message'   => 'Brand status updated successfully.',
            'is_active' => $isActive
        ]);
    }

    /**
     * Reorder brands via AJAX.
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:brands,id',
        ]);

        return $this->reorderItems($validated['order'], Brand::class);
    }

    /**
     * Resolve dashboard filters from request.
     *
     * @return array{0:string,1:string}
     */
    private function resolveFilters(Request $request): array
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');

        return [$search, $status];
    }
}
