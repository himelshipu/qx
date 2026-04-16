<?php

namespace App\Services\Admin;

use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Collection;

class SettingService
{
    public function __construct(
        private SettingRepositoryInterface $repository,
    ) {}

    /**
     * Get branding settings for display.
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
     */
    public function getPlatformSettings(): array
    {
        return [
            'charge_type' => $this->repository->get('platform.charge_type', 'percentage'),
            'charge_value' => $this->repository->get('platform.charge_value', 10),
        ];
    }

    /**
     * Get footer pages settings for display.
     */
    public function getFooterSettings(): array
    {
        return (array) $this->repository->get('footer_pages', []);
    }

    /**
     * Update branding settings.
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
     * Update email settings.
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
     * Update platform settings.
     */
    public function updatePlatformSettings(array $data): bool
    {
        return $this->repository->updateSection('platform', [
            'charge_type' => $data['charge_type'],
            'charge_value' => $data['charge_value'],
        ]);
    }

    /**
     * Update footer pages settings.
     */
    public function updateFooterSettings(array $selectedPages, ?string $orderedPagesJson): bool
    {
        $orderedPages = $selectedPages;

        if (!empty($orderedPagesJson)) {
            $decoded = json_decode($orderedPagesJson, true);
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

        return $this->repository->set('footer_pages', $orderedPages);
    }

    /**
     * Reorder footer pages.
     */
    public function reorderFooterPages(array $pageIds): bool
    {
        // Validate and normalize IDs
        $ids = array_values(array_filter(
            array_unique(array_map('intval', $pageIds)),
            fn (int $id): bool => $id > 0
        ));

        // Save the new order
        return $this->repository->set('footer_pages', $ids);
    }

    /**
     * Get recovery items from all trashed models.
     */
    public function getRecoveryItems(int $limit = 100): Collection
    {
        // This will be handled by a separate RecoveryService, but kept here for reference
        return collect();
    }
}
