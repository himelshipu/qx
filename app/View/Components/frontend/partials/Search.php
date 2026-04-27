<?php

namespace App\View\Components\frontend\partials;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Search extends Component
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
        return view('components.frontend.partials.search', [
            'regionOptions' => $this->regionOptions,
            'genderOptions' => $this->genderOptions,
            'followerRangeOptions' => $this->followerRangeOptions,
        ]);
    }
}
