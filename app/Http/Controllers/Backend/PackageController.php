<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Package\StorePackageRequest;
use App\Http\Requests\Backend\Package\UpdatePackageRequest;
use App\Models\Package;
use App\Services\Admin\PackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function __construct(
        private readonly PackageService $packageService
    ) {}

    /**
     * Display a listing of packages with search and filters.
     */
    public function index(Request $request): View
    {
        $search   = trim((string) $request->string('q', ''));
        $status   = (string) $request->string('status', 'all');
        $platform = (string) $request->string('platform', 'all');

        return view('backend.pages.packages.index', $this->packageService->getListingPayload($search, $status, $platform));
    }

    /**
     * Show the form for creating a new package.
     */
    public function create(): View
    {
        return view('backend.pages.packages.create', $this->packageService->getFormPayload());
    }

    /**
     * Store a newly created package in storage.
     */
    public function store(StorePackageRequest $request): RedirectResponse
    {
        $this->packageService->createPackage(
            $request->validated(),
            $request->boolean('is_active', true)
        );

        return redirect()
            ->route('dashboard.packages.index')
            ->with('success', 'Package created successfully.');
    }

    /**
     * Show the form for editing the specified package.
     */
    public function edit(Package $package): View
    {
        return view('backend.pages.packages.edit', array_merge(
            $this->packageService->getFormPayload(),
            ['package' => $package]
        ));
    }

    /**
     * Update the specified package in storage.
     */
    public function update(UpdatePackageRequest $request, Package $package): RedirectResponse
    {
        $this->packageService->updatePackage(
            $package,
            $request->validated(),
            $request->boolean('is_active')
        );

        return redirect()
            ->route('dashboard.packages.index')
            ->with('success', 'Package updated successfully.');
    }

    /**
     * Remove the specified package if it has no dependencies.
     */
    public function destroy(Package $package): RedirectResponse
    {
        $result = $this->packageService->deletePackage($package);

        if (!$result['deleted']) {
            return redirect()
                ->route('dashboard.packages.index')
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('dashboard.packages.index')
            ->with('success', $result['message']);
    }

    /**
     * Toggle package status.
     */
    public function toggleStatus(Package $package): JsonResponse
    {
        $isActive = $this->packageService->toggleStatus($package);

        return response()->json([
            'success'   => true,
            'message'   => 'Package status updated successfully.',
            'is_active' => $isActive
        ]);
    }
}
