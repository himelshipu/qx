<?php

namespace App\View\Components\Backend\Dropdowns;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Menu extends Component
{
    public function __construct()
    {
        //
    }

    public function render(): View|Closure|string
    {
        return view('components.backend.dropdowns.menu');
    }
}