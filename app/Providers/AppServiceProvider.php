<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Creator;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'brand'   => Brand::class,
            'creator' => Creator::class
        ]);
    }
}
