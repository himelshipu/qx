<?php

namespace App\View\Components\backend\dropdowns;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class User extends Component
{
    public $user;
    public $avatarUrl;
    public $initials;
    public $firstName;

    public function __construct()
    {
        $this->user = Auth::user();

        $avatarPath = $this->user?->brand?->profile_image_path
            ?? $this->user?->creator?->profile_image_path
            ?? $this->user?->profile_image_path;

        $this->avatarUrl = filled($avatarPath) ? image_url($avatarPath) : null;

        $this->initials = Str::of($this->user?->name ?? 'U')
            ->explode(' ')
            ->map(fn ($word) => Str::upper($word[0] ?? ''))
            ->filter()
            ->take(2)
            ->join('');

        $this->firstName = Str::before($this->user?->name ?? 'User', ' ');
    }

    public function render(): View|Closure|string
    {
        return view('components.backend.dropdowns.user');
    }
}