<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
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
use App\Models\Setting;
use App\Models\StaticPage;
use App\Models\SupportTicket;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Throwable;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('permission:settings.index')->only(['index']);
        $this->middleware('permission:settings.update')->only(['update']);
        $this->middleware('permission:settings.restore')->only(['restoreEntity']);
    }

    /**
     * Display the settings page.
     */
    public function index(): View
    {
        $pages = StaticPage::orderBy('title')->get();
        $footerPages = Setting::get('footer_pages', []);
        $recoveryItems = $this->buildRecoveryItems();
        $activeTab = (string) request()->string('tab', 'branding');

        return view('backend.pages.settings.index', [
            'activeTab' => in_array($activeTab, ['branding', 'email', 'platform', 'footer', 'recovery'], true) ? $activeTab : 'branding',
            'pages' => $pages,
            'footerPages' => $footerPages,
            'brandingSettings' => [
                'site_name' => Setting::get('branding.site_name', config('app.name')),
                'tagline' => Setting::get('branding.tagline', ''),
                'logo_light' => Setting::fileUrl('branding.logo_light', '/images/logo/header-logo.png'),
                'logo_dark' => Setting::fileUrl('branding.logo_dark', '/images/logo/header-logo.png'),
                'favicon' => Setting::fileUrl('branding.favicon', '/default.webp'),
            ],
            'emailSettings' => [
                'mailer' => Setting::get('email.mailer', config('mail.default')),
                'host' => Setting::get('email.host', config('mail.mailers.smtp.host')),
                'port' => Setting::get('email.port', config('mail.mailers.smtp.port')),
                'username' => Setting::get('email.username', config('mail.mailers.smtp.username')),
                'password' => Setting::get('email.password', config('mail.mailers.smtp.password')),
                'encryption' => Setting::get('email.encryption', config('mail.mailers.smtp.encryption')),
                'from_name' => Setting::get('email.from_name', config('mail.from.name')),
                'from_address' => Setting::get('email.from_address', config('mail.from.address')),
            ],
            'platformSettings' => [
                'charge_type' => Setting::get('platform.charge_type', 'percentage'),
                'charge_value' => Setting::get('platform.charge_value', 10),
            ],
            'recoveryItems' => $recoveryItems,
        ]);
    }

    /**
     * Update settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $section = (string) $request->string('section', 'branding');

        switch ($section) {
            case 'branding':
                $validated = $request->validate([
                    'site_name' => 'required|string|max:255',
                    'tagline' => 'nullable|string|max:255',
                    'logo_light' => 'nullable|image|mimes:png,jpg,jpeg,webp,avif,gif,svg|max:6144',
                    'logo_dark' => 'nullable|image|mimes:png,jpg,jpeg,webp,avif,gif,svg|max:6144',
                    'favicon' => 'nullable|image|mimes:png,ico,svg|max:2048',
                ]);

                $branding = [
                    'site_name' => $validated['site_name'],
                    'tagline' => $validated['tagline'] ?? '',
                    'logo_light' => Setting::get('branding.logo_light', '/images/logo/header-logo.png'),
                    'logo_dark' => Setting::get('branding.logo_dark', '/images/logo/header-logo.png'),
                    'favicon' => Setting::get('branding.favicon', '/default.webp'),
                ];

                if ($request->hasFile('logo_light')) {
                    $branding['logo_light'] = $request->file('logo_light')->store('settings/branding', 'public');
                }

                if ($request->hasFile('logo_dark')) {
                    $branding['logo_dark'] = $request->file('logo_dark')->store('settings/branding', 'public');
                }

                if ($request->hasFile('favicon')) {
                    $branding['favicon'] = $request->file('favicon')->store('settings/branding', 'public');
                }

                Setting::set('branding.site_name', $branding['site_name']);
                Setting::set('branding.tagline', $branding['tagline']);
                Setting::set('branding.logo_light', $branding['logo_light']);
                Setting::set('branding.logo_dark', $branding['logo_dark']);
                Setting::set('branding.favicon', $branding['favicon']);
                break;

            case 'email':
                $validated = $request->validate([
                    'mailer' => 'nullable|string|max:50',
                    'host' => 'nullable|string|max:255',
                    'port' => 'nullable|integer|min:1|max:65535',
                    'username' => 'nullable|string|max:255',
                    'password' => 'nullable|string|max:255',
                    'encryption' => 'nullable|string|max:20',
                    'from_name' => 'nullable|string|max:255',
                    'from_address' => 'nullable|email|max:255',
                ]);

                Setting::set('email.mailer', $validated['mailer'] ?? 'smtp');
                Setting::set('email.host', $validated['host'] ?? '');
                Setting::set('email.port', $validated['port'] ?? '');
                Setting::set('email.username', $validated['username'] ?? '');
                Setting::set('email.password', $validated['password'] ?? '');
                Setting::set('email.encryption', $validated['encryption'] ?? '');
                Setting::set('email.from_name', $validated['from_name'] ?? config('app.name'));
                Setting::set('email.from_address', $validated['from_address'] ?? '');
                break;

            case 'platform':
                $validated = $request->validate([
                    'charge_type' => 'required|in:percentage,fixed',
                    'charge_value' => 'required|numeric|min:0',
                ]);

                Setting::set('platform.charge_type', $validated['charge_type']);
                Setting::set('platform.charge_value', $validated['charge_value']);
                break;

            case 'footer':
                $validated = $request->validate([
                    'footer_pages' => 'nullable|array',
                    'footer_pages.*' => 'exists:static_pages,id',
                    'footer_pages_order' => 'nullable|string',
                ]);

                $selectedPages = $validated['footer_pages'] ?? [];
                $orderedPages = $selectedPages;

                if (!empty($validated['footer_pages_order'])) {
                    $decoded = json_decode((string) $validated['footer_pages_order'], true);
                    if (is_array($decoded)) {
                        $decoded = array_map('intval', $decoded);
                        $selectedMap = array_flip(array_map('intval', $selectedPages));
                        $orderedPages = array_values(array_filter($decoded, fn ($id) => isset($selectedMap[$id])));

                        foreach ($selectedPages as $id) {
                            if (!in_array((int) $id, $orderedPages, true)) {
                                $orderedPages[] = (int) $id;
                            }
                        }
                    }
                }

                Setting::set('footer_pages', $orderedPages);
                break;

            default:
                return redirect()
                    ->route('dashboard.settings.index')
                    ->with('error', 'Invalid settings section submitted.');
        }

        return redirect()
            ->route('dashboard.settings.index', ['tab' => $section])
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Update page order via AJAX
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:static_pages,id',
        ]);

        Setting::set('footer_pages', $validated['order']);

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
                $records = $modelClass::onlyTrashed()->latest('deleted_at')->limit(100)->get();
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
