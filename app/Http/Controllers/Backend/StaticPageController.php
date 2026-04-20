<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StaticPage\StoreStaticPageRequest;
use App\Http\Requests\Backend\StaticPage\UpdateStaticPageRequest;
use App\Models\StaticPage;
use App\Services\Admin\StaticPageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    public function __construct(
        private readonly StaticPageService $service
    ) {
        $this->middleware('permission:static-pages.index')->only(['index', 'table']);
        $this->middleware('permission:static-pages.create')->only(['create', 'store']);
        $this->middleware('permission:static-pages.show')->only(['show']);
        $this->middleware('permission:static-pages.edit')->only(['edit', 'update']);
        $this->middleware('permission:static-pages.toggle-status')->only(['toggleStatus']);
        $this->middleware('permission:static-pages.destroy')->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');

        $payload = $this->service->getListingPayload($search, $status);
        $payload['pages']->appends([
            'q' => $search,
            'status' => $status,
        ]);

        return view('backend.pages.static-pages.index', $payload);
    }

    public function table(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');

        $pages = $this->service->getListingPayload($search, $status)['pages'];
        $pages->appends([
            'q' => $search,
            'status' => $status,
        ]);

        return view('backend.pages.static-pages._results', [
            'pages' => $pages,
        ]);
    }

    public function create(): View
    {
        return view('backend.pages.static-pages.create', [
            'page' => new StaticPage,
            'nextSortOrder' => $this->service->getNextSortOrder(),
        ]);
    }

    public function store(StoreStaticPageRequest $request): RedirectResponse
    {
        $page = $this->service->createStaticPage($request->validated(), $request->boolean('is_active'));

        return redirect()
            ->route('dashboard.static-pages.show', $page)
            ->with('success', "Static page '{$page->title}' created successfully.");
    }

    public function show(StaticPage $staticPage): View
    {
        return view('backend.pages.static-pages.show', [
            'page' => $staticPage,
        ]);
    }

    public function edit(StaticPage $staticPage): View
    {
        return view('backend.pages.static-pages.edit', [
            'page' => $staticPage,
        ]);
    }

    public function update(UpdateStaticPageRequest $request, StaticPage $staticPage): RedirectResponse
    {
        $page = $this->service->updateStaticPage($staticPage, $request->validated(), $request->boolean('is_active'));

        return redirect()
            ->route('dashboard.static-pages.show', $page)
            ->with('success', "Static page '{$page->title}' updated successfully.");
    }

    public function toggleStatus(Request $request, StaticPage $staticPage): JsonResponse|RedirectResponse
    {
        $page = $this->service->toggleStatus($staticPage);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Static page '{$page->title}' status updated successfully.",
                'is_active' => (bool) $page->is_active,
            ]);
        }

        return redirect()
            ->back()
            ->with('success', "Static page '{$page->title}' status updated successfully.");
    }

    public function destroy(StaticPage $staticPage): RedirectResponse
    {
        $title = $staticPage->title;
        $this->service->deleteStaticPage($staticPage);

        return redirect()
            ->route('dashboard.static-pages.index')
            ->with('success', "Static page '{$title}' deleted successfully.");
    }
}
