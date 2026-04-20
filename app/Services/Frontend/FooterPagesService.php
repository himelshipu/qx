<?php

declare(strict_types=1);

namespace App\Services\Frontend;

use App\Models\Setting;
use App\Models\StaticPage;
use Illuminate\Support\Collection;

final class FooterPagesService
{
    /**
     * Get footer pages in the order specified in admin settings.
     * If no setting exists, returns all active static pages in natural order.
     *
     * @return Collection<int, StaticPage>
     */
    public function getFooterPages(): Collection
    {
        $footerPageIds = Setting::get('footer_pages', []);

        $footerPageIds = array_values(array_map('intval', (array) $footerPageIds));

        // If setting is empty, return all active pages
        if (empty($footerPageIds)) {
            return StaticPage::where('is_active', true)
                ->where('show_on_footer', true)
                ->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();
        }

        // Get pages that exist and are active
        $pages = StaticPage::whereIn('id', $footerPageIds)
            ->where('is_active', true)
            ->where('show_on_footer', true)
            ->get()
            ->keyBy('id');

        // Sort in the order specified in settings
        $orderedPages = collect();
        foreach ($footerPageIds as $id) {
            if ($pages->has($id)) {
                $orderedPages->push($pages->get($id));
            }
        }

        return $orderedPages->values();
    }

    /**
     * Get site name from settings.
     */
    public function getSiteName(): string
    {
        return Setting::get('branding.site_name', config('app.name', 'Rockies'));
    }
}
