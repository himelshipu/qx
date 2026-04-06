<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Package\StorePackageRequest;
use App\Http\Requests\Backend\Package\UpdatePackageRequest;
use App\Models\Package;
use App\Services\Admin\PackageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function __construct(
        private readonly PackageService $packageService
    ) {}

    /**
     * Display a listing of packages for the creator.
     * Creators see their own packages.
     * Public users can browse all public packages.
     */
    public function index(Request $request): View
    {
        $user     = auth()->user();
        $search   = trim((string) $request->input('q', ''));
        $platform = (string) $request->input('platform', 'all');

        if ($user->user_type === 'creator') {
            // Creator sees only their own packages
            $creator = $user->creator;

            $packages = Package::where('creator_id', $creator->id)
                ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
                ->when($platform !== 'all', fn($q) => $q->where('platform', $platform))
                ->where('is_active', true)
                ->orderByDesc('created_at')
                ->paginate(12);

            return view('frontend.packages.creator-index', compact('packages', 'search', 'platform'));
        } else {
            // Non-creators (guests, brands) see published packages
            $packages = Package::where('is_active', true)
                ->with('creator.user')
                ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
                ->when($platform !== 'all', fn($q) => $q->where('platform', $platform))
                ->orderByDesc('created_at')
                ->paginate(12);

            return view('frontend.packages.index', compact('packages', 'search', 'platform'));
        }
    }

    /**
     * Show the form for creating a new package.
     */
    public function create(): View
    {
        $user = auth()->user();

        if ($user->user_type !== 'creator') {
            abort(403, 'Only creators can create packages');
        }

        return view('frontend.packages.create', $this->packageService->getFormPayload());
    }

    /**
     * Store a newly created package.
     */
    public function store(StorePackageRequest $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->user_type !== 'creator') {
            abort(403, 'Only creators can create packages');
        }

        $data               = $request->validated();
        $data['creator_id'] = $user->creator->id;

        $this->packageService->createPackage(
            $data,
            $request->boolean('is_active', true)
        );

        return redirect()
            ->route('frontend.packages.index')
            ->with('success', 'Package created successfully.');
    }

    /**
     * Display the specified package.
     */
    public function show(Package $package): View
    {
        $package->load(['creator.user', 'orderItems']);

        return view('frontend.packages.show', compact('package'));
    }

    /**
     * Show the form for editing the specified package.
     */
    public function edit(Package $package): View
    {
        $user = auth()->user();

        // Only creator who owns the package can edit
        if ($user->user_type !== 'creator' || $package->creator_id !== $user->creator->id) {
            abort(403, 'Unauthorized');
        }

        return view('frontend.packages.edit', array_merge(
            $this->packageService->getFormPayload(),
            ['package' => $package]
        ));
    }

    /**
     * Update the specified package.
     */
    public function update(UpdatePackageRequest $request, Package $package): RedirectResponse
    {
        $user = auth()->user();

        // Only creator who owns the package can update
        if ($user->user_type !== 'creator' || $package->creator_id !== $user->creator->id) {
            abort(403, 'Unauthorized');
        }

        $this->packageService->updatePackage(
            $package,
            $request->validated(),
            $request->boolean('is_active')
        );

        return redirect()
            ->route('frontend.packages.index')
            ->with('success', 'Package updated successfully.');
    }

    /**
     * Delete the specified package.
     */
    public function destroy(Package $package): RedirectResponse
    {
        $user = auth()->user();

        // Only creator who owns the package can delete
        if ($user->user_type !== 'creator' || $package->creator_id !== $user->creator->id) {
            abort(403, 'Unauthorized');
        }

        $packageName = $package->name;
        $package->delete();

        return redirect()
            ->route('frontend.packages.index')
            ->with('success', "Package \"{$packageName}\" deleted successfully.");
    }
}
