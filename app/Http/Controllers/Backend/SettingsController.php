<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Setting\RestoreSettingEntityRequest;
use App\Http\Requests\Backend\Setting\UpdateBrandingSettingRequest;
use App\Http\Requests\Backend\Setting\UpdateEmailSettingRequest;
use App\Http\Requests\Backend\Setting\UpdateFooterOrderSettingRequest;
use App\Http\Requests\Backend\Setting\UpdateFooterSettingRequest;
use App\Http\Requests\Backend\Setting\UpdatePlatformSettingRequest;
use App\Services\Admin\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        private readonly SettingService $service,
    ) {
        $this->middleware('permission:settings.index')->only(['index']);
        $this->middleware('permission:settings.update')->only([
            'updateBranding',
            'updateEmail',
            'updatePlatform',
            'updateFooter',
            'updateOrder',
        ]);
        $this->middleware('permission:settings.restore')->only(['restoreEntity']);
    }

    /**
     * Display the settings page.
     */
    public function index(): View
    {
        $activeTab = (string) request()->string('tab', 'branding');

        return view('backend.pages.settings.index', $this->service->getIndexPayload($activeTab));
    }

    /**
     * Update branding settings.
     */
    public function updateBranding(UpdateBrandingSettingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('logo_light')) {
            $validated['logo_light'] = $request->file('logo_light')->store('settings/branding', 'public');
        }

        if ($request->hasFile('logo_dark')) {
            $validated['logo_dark'] = $request->file('logo_dark')->store('settings/branding', 'public');
        }

        if ($request->hasFile('favicon')) {
            $validated['favicon'] = $request->file('favicon')->store('settings/branding', 'public');
        }

        $this->service->updateBrandingSettings($validated);

        return redirect()
            ->route('dashboard.settings.index', ['tab' => 'branding'])
            ->with('success', 'Branding settings updated successfully.');
    }

    /**
     * Update email settings.
     */
    public function updateEmail(UpdateEmailSettingRequest $request): RedirectResponse
    {
        $this->service->updateEmailSettings($request->validated());

        return redirect()
            ->route('dashboard.settings.index', ['tab' => 'email'])
            ->with('success', 'Email settings updated successfully.');
    }

    /**
     * Update platform settings.
     */
    public function updatePlatform(UpdatePlatformSettingRequest $request): RedirectResponse
    {
        $this->service->updatePlatformSettings($request->validated());

        return redirect()
            ->route('dashboard.settings.index', ['tab' => 'platform'])
            ->with('success', 'Platform settings updated successfully.');
    }

    /**
     * Update footer settings.
     */
    public function updateFooter(UpdateFooterSettingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->service->updateFooterSettings(
            $validated['footer_pages'] ?? [],
            $validated['footer_pages_order'] ?? null
        );

        return redirect()
            ->route('dashboard.settings.index', ['tab' => 'footer'])
            ->with('success', 'Footer settings updated successfully.');
    }

    /**
     * Update page order via AJAX.
     */
    public function updateOrder(UpdateFooterOrderSettingRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $this->service->reorderFooterPages($validated['order']);

        return response()->json([
            'success' => true,
            'message' => 'Page order updated successfully',
        ]);
    }

    public function restoreEntity(RestoreSettingEntityRequest $request, string $type, int $id): RedirectResponse
    {
        $message = $this->service->restoreEntity($type, $id);

        return redirect()
            ->route('dashboard.settings.index', ['tab' => 'recovery'])
            ->with('success', $message);
    }
}
