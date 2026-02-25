<?php

namespace App\View\Components\frontend\navigation;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AuthHeader extends Component
{
    public function __construct()
    {
        //
    }

    public function render(): View|Closure|string
    {
        return view('components.frontend.navigation.auth-header');
    }
}
