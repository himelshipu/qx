<?php

namespace App\View\Components\frontend\navigation;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Hero extends Component
{
    public array $regionOptions;
    public array $genderOptions;
    public array $followerRangeOptions;

    public function __construct(
        array $regionOptions = [],
        array $genderOptions = [],
        array $followerRangeOptions = []
    ) {
        $this->regionOptions = $regionOptions;
        $this->genderOptions = $genderOptions;
        $this->followerRangeOptions = $followerRangeOptions;
    }

    public function render(): View|Closure|string
    {
        return view('components.frontend.navigation.hero', [
            'regionOptions' => $this->regionOptions,
            'genderOptions' => $this->genderOptions,
            'followerRangeOptions' => $this->followerRangeOptions,
        ]);
    }
}
