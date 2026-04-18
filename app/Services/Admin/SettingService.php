<?php

declare (strict_types = 1);

namespace App\Services\Admin;

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
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Throwable;

final class SettingService
{
    public function __construct(
        private readonly SettingRepositoryInterface $repository,
    ) {
    }

    /**
     * @return array{activeTab:string,pages:Collection<int,StaticPage>,footerPages:array<int,int>,brandingSettings:array<string,mixed>,emailSettings:array<string,mixed>,platformSettings:array<string,mixed>,recoveryItems:Collection<int,array<string,mixed>>}
     */
    public function getIndexPayload(string $activeTab): array
    {
        $resolvedTab = in_array($activeTab, ['branding', 'email', 'platform', 'footer', 'recovery'], true)
            ? $activeTab
            : 'branding';

        $footerPageIds = $this->getFooterSettings();

        return [
            'activeTab' => $resolvedTab,
            'pages' => $this->getOrderedFooterPages($footerPageIds),
            'footerPages' => $footerPageIds,
            'brandingSettings' => $this->getBrandingSettings(),
            'emailSettings' => $this->getEmailSettings(),
            'platformSettings' => $this->getPlatformSettings(),
            'recoveryItems' => $this->buildRecoveryItems(),
        ];
    }

    /**
     * Get branding settings for display.
     *
     * @return array<string,mixed>
     */
    public function getBrandingSettings(): array
    {
        return [
            'site_name' => $this->repository->get('branding.site_name', config('app.name')),
            'tagline' => $this->repository->get('branding.tagline', ''),
            'logo_light' => $this->repository->fileUrl('branding.logo_light', '/images/logo/header-logo.png'),
            'logo_dark' => $this->repository->fileUrl('branding.logo_dark', '/images/logo/header-logo.png'),
            'favicon' => $this->repository->fileUrl('branding.favicon', '/default.webp'),
        ];
    }

    /**
     * Get email settings for display.
     *
     * @return array<string,mixed>
     */
    public function getEmailSettings(): array
    {
        return [
            'mailer' => $this->repository->get('email.mailer', config('mail.default')),
            'host' => $this->repository->get('email.host', config('mail.mailers.smtp.host')),
            'port' => $this->repository->get('email.port', config('mail.mailers.smtp.port')),
            'username' => $this->repository->get('email.username', config('mail.mailers.smtp.username')),
            'password' => $this->repository->get('email.password', config('mail.mailers.smtp.password')),
            'encryption' => $this->repository->get('email.encryption', config('mail.mailers.smtp.encryption')),
            'from_name' => $this->repository->get('email.from_name', config('mail.from.name')),
            'from_address' => $this->repository->get('email.from_address', config('mail.from.address')),
        ];
    }

    /**
     * Get platform settings for display.
     *
     * @return array<string,mixed>
     */
    public function getPlatformSettings(): array
    {
        return [
            'charge_type' => $this->repository->get('platform.charge_type', 'percentage'),
            'charge_value' => $this->repository->get('platform.charge_value', 10),
        ];
    }

    /**
     * @return array<int,int>
     */
    public function getFooterSettings(): array
    {
        return array_values(array_map('intval', (array) $this->repository->get('footer_pages', [])));
    }

    /**
     * @param array<string,mixed> $data
     */
    public function updateBrandingSettings(array $data): bool
    {
        return $this->repository->updateSection('branding', [
            'site_name' => $data['site_name'],
            'tagline' => $data['tagline'] ?? '',
            'logo_light' => $data['logo_light'] ?? $this->repository->get('branding.logo_light'),
            'logo_dark' => $data['logo_dark'] ?? $this->repository->get('branding.logo_dark'),
            'favicon' => $data['favicon'] ?? $this->repository->get('branding.favicon'),
        ]);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function updateEmailSettings(array $data): bool
    {
        return $this->repository->updateSection('email', [
            'mailer' => $data['mailer'] ?? 'smtp',
            'host' => $data['host'] ?? '',
            'port' => $data['port'] ?? '',
            'username' => $data['username'] ?? '',
            'password' => $data['password'] ?? '',
            'encryption' => $data['encryption'] ?? '',
            'from_name' => $data['from_name'] ?? config('app.name'),
            'from_address' => $data['from_address'] ?? '',
        ]);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function updatePlatformSettings(array $data): bool
    {
        return $this->repository->updateSection('platform', [
            'charge_type' => $data['charge_type'],
            'charge_value' => $data['charge_value'],
        ]);
    }

    /**
     * @param array<int,int|string> $selectedPages
     */
    public function updateFooterSettings(array $selectedPages, ?string $orderedPagesJson): bool
    {
        $normalizedSelected = array_values(array_unique(array_map('intval', $selectedPages)));
        $orderedPages = $normalizedSelected;

        if (!empty($orderedPagesJson)) {
            $decoded = json_decode($orderedPagesJson, true);
            if (is_array($decoded)) {
                $decoded = array_values(array_unique(array_map('intval', $decoded)));
                $selectedMap = array_flip($normalizedSelected);
                $orderedPages = array_values(array_filter($decoded, fn ($id) => isset($selectedMap[$id])));

                foreach ($normalizedSelected as $id) {
                    if (!in_array($id, $orderedPages, true)) {
                        $orderedPages[] = $id;
                    }
                }
            }
        }

        return $this->repository->set('footer_pages', $orderedPages);
    }

    /**
     * @param array<int,int|string> $pageIds
     */
    public function reorderFooterPages(array $pageIds): bool
    {
        $ids = array_values(array_filter(
            array_unique(array_map('intval', $pageIds)),
            fn (int $id): bool => $id > 0
        ));

        return $this->repository->set('footer_pages', $ids);
    }

    public function restoreEntity(string $type, int $id): string
    {
        $map = $this->getRecoveryModelMap();

        if (!isset($map[$type])) {
            throw new \RuntimeException('Unknown recovery type.');
        }

        $this->repository->restoreTrashedRecord($map[$type]['model'], $id);

        return $map[$type]['label'] . ' restored successfully.';
    }

    /**
     * @return array<string, array{label:string, model:class-string<Model>}>
     */
    public function getRecoveryModelMap(): array
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
     * @return Collection<int,array<string,mixed>>
     */
    private function buildRecoveryItems(): Collection
    {
        $items = collect();
        $limit = (int) config('settings.dashboard.max_recovery_items', 100);

        foreach ($this->getRecoveryModelMap() as $type => $meta) {
            try {
                $records = $this->repository->getTrashedRecords($meta['model'], $limit);
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

    /**
     * @param array<int,int> $footerPageIds
     * @return Collection<int,StaticPage>
     */
    private function getOrderedFooterPages(array $footerPageIds): Collection
    {
        $allPages = $this->repository->getAllStaticPages();

        return $allPages
            ->sortBy(function (StaticPage $page) use ($footerPageIds): int {
                $position = array_search((int) $page->id, $footerPageIds, true);

                return $position !== false ? $position : PHP_INT_MAX;
            })
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
