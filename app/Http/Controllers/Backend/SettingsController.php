<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Setting\UpdateBrandingSettingRequest;
use App\Http\Requests\Backend\Setting\UpdateEmailSettingRequest;
use App\Http\Requests\Backend\Setting\UpdatePlatformSettingRequest;
use App\Http\Requests\Backend\Setting\UpdateFooterSettingRequest;
use App\Models\BlogPost;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\CampaignAsset;
use App\Models\CampaignInfluencer;
use App\Models\CampaignTargetCountry;
use App\Models\CampaignTargeting;
use App\Models\CaseStudy;
use App\Models\Category;
use App\Models\Influencer;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\Review;
use App\Models\Role;
use App\Models\StaticPage;
use App\Models\SupportTicket;
use App\Models\Testimonial;
use App\Services\Admin\SettingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Throwable;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private SettingService $service,
    ) {
        $this->middleware('permission:settings.index')->only(['index']);
        $this->middleware('permission:settings.update')->only(['update']);
        $this->middleware('permission:settings.restore')->only(['restoreEntity']);
    }

    /**
     * Display the settings page.
     */
    public function index(): View
    {
        $footerPageIds = $this->service->getFooterSettings();
        $allPages = StaticPage::all();
        
        // Reorder pages based on footer_pages setting
        $pages = $allPages->sortBy(function ($page) use ($footerPageIds) {
            $position = array_search($page->id, $footerPageIds);
            return $position !== false ? $position : PHP_INT_MAX;
        })->values();

        $recoveryItems = $this->buildRecoveryItems();
        $activeTab = (string) request()->string('tab', 'branding');

        return view('backend.pages.settings.index', [
            'activeTab' => in_array($activeTab, ['branding', 'email', 'platform', 'footer', 'recovery'], true) ? $activeTab : 'branding',
            'pages' => $pages,
            'footerPages' => $footerPageIds,
            'brandingSettings' => $this->service->getBrandingSettings(),
            'emailSettings' => $this->service->getEmailSettings(),
            'platformSettings' => $this->service->getPlatformSettings(),
            'recoveryItems' => $recoveryItems,
        ]);
    }

    /**
     * Update branding settings.
     */
    public function updateBranding(UpdateBrandingSettingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Handle file uploads
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
     * Update page order via AJAX
     */
    public function updateOrder(UpdateFooterSettingRequest $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:static_pages,id',
        ]);

        $this->service->reorderFooterPages($validated['order']);

        return response()->json([
            'success' => true,
            'message' => 'Page order updated successfully'
        ]);
    }

    public function restoreEntity(string $type, int $id): RedirectResponse
    {
        $map = $this->getRecoveryModelMap();

        if (!isset($map[$type])) {
            abort(404, 'Unknown recovery type.');
        }

        /** @var class-string<Model> $modelClass */
        $modelClass = $map[$type]['model'];
        $entry = $modelClass::withTrashed()->findOrFail($id);
        $entry->restore();

        return redirect()
            ->route('dashboard.settings.index', ['tab' => 'recovery'])
            ->with('success', $map[$type]['label'] . ' restored successfully.');
    }

    /**
     * @return array<string, array{label: string, model: class-string<Model>}>
     */
    private function getRecoveryModelMap(): array
    {
        return [
            'blog-post' => ['label' => 'Blog Post', 'model' => BlogPost::class],
            'static-page' => ['label' => 'Static Page', 'model' => StaticPage::class],
            'brand' => ['label' => 'Brand', 'model' => Brand::class],
            'influencer' => ['label' => 'Influencer', 'model' => Influencer::class],
            'campaign' => ['label' => 'Campaign', 'model' => Campaign::class],
            'campaign-application' => ['label' => 'Campaign Application', 'model' => CampaignApplication::class],
            'campaign-asset' => ['label' => 'Campaign Asset', 'model' => CampaignAsset::class],
            'campaign-influencer' => ['label' => 'Campaign Influencer', 'model' => CampaignInfluencer::class],
            'campaign-target-country' => ['label' => 'Campaign Target Country', 'model' => CampaignTargetCountry::class],
            'campaign-targeting' => ['label' => 'Campaign Targeting', 'model' => CampaignTargeting::class],
            'package' => ['label' => 'Package', 'model' => Package::class],
            'order' => ['label' => 'Order', 'model' => Order::class],
            'category' => ['label' => 'Category', 'model' => Category::class],
            'review' => ['label' => 'Review', 'model' => Review::class],
            'testimonial' => ['label' => 'Testimonial', 'model' => Testimonial::class],
            'case-study' => ['label' => 'Case Study', 'model' => CaseStudy::class],
            'support-ticket' => ['label' => 'Support Ticket', 'model' => SupportTicket::class],
            'payment' => ['label' => 'Payment', 'model' => Payment::class],
            'payout' => ['label' => 'Payout', 'model' => Payout::class],
            'notification' => ['label' => 'Notification', 'model' => Notification::class],
            'role' => ['label' => 'Role', 'model' => Role::class],
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function buildRecoveryItems(): Collection
    {
        $items = collect();

        foreach ($this->getRecoveryModelMap() as $type => $meta) {
            /** @var class-string<Model> $modelClass */
            $modelClass = $meta['model'];
            try {
                $records = $modelClass::onlyTrashed()->latest('deleted_at')->limit(config('settings.dashboard.max_recovery_items'))->get();
            } catch (Throwable $exception) {
                continue;
            }

            foreach ($records as $record) {
                $items->push([
                    'type' => $type,
                    'type_label' => $meta['label'],
                    'id' => (int) $record->getKey(),
                    'title' => $this->resolveRecoveryTitle($record),
                    'identifier' => $this->resolveRecoveryIdentifier($record),
                    'deleted_at' => $record->deleted_at,
                ]);
            }
        }

        return $items
            ->sortByDesc(fn (array $item) => optional($item['deleted_at'])->timestamp ?? 0)
            ->values();
    }

    private function resolveRecoveryTitle(Model $record): string
    {
        $candidates = [
            'title',
            'name',
            'brand_name',
            'display_name',
            'subject',
            'message',
            'slug',
            'id',
        ];

        foreach ($candidates as $key) {
            $value = $record->getAttribute($key);
            if (is_scalar($value) && trim((string) $value) !== '') {
                return (string) $value;
            }
        }

        return 'Record #' . $record->getKey();
    }

    private function resolveRecoveryIdentifier(Model $record): string
    {
        $candidates = [
            'order_number',
            'ticket_number',
            'slug',
            'email',
            'status',
        ];

        foreach ($candidates as $key) {
            $value = $record->getAttribute($key);
            if (is_scalar($value) && trim((string) $value) !== '') {
                return strtoupper(str_replace('_', ' ', $key)) . ': ' . (string) $value;
            }
        }

        return 'ID: ' . $record->getKey();
    }
}
