<?php

namespace App\View\Components\Backend\Shell;

use App\Helpers\MenuHelper;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public array $menuItems;
    public string $currentRoute;
    public ?string $activeAccordion;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->currentRoute = Route::currentRouteName() ?? '';
        $sidebarData = MenuHelper::buildSidebarMenu($this->currentRoute);
        $this->menuItems = $sidebarData['items'];
        $this->activeAccordion = $sidebarData['activeAccordion'];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.backend.shell.sidebar');
    }
}