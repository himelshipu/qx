<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Services\Frontend\FooterPagesService;
use Illuminate\View\View;

final class FooterComposer
{
    public function __construct(
        private readonly FooterPagesService $footerPagesService,
    ) {
    }

    public function compose(View $view): void
    {
        $view->with([
            'footerPages' => $this->footerPagesService->getFooterPages(),
            'siteName' => $this->footerPagesService->getSiteName(),
        ]);
    }
}
