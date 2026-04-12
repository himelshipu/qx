<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Conversation;
use App\Models\Influencer;
use App\Models\Order;
use App\Models\Package;
use App\Models\Role;
use App\Policies\CampaignPolicy;
use App\Policies\ConversationPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PackagePolicy;
use App\Policies\RolePolicy;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
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
            'brand'      => Brand::class,
            'influencer' => Influencer::class
        ]);

        // Register authorization policies
        Gate::policy(Campaign::class, CampaignPolicy::class);
        Gate::policy(Package::class, PackagePolicy::class);
        Gate::policy(Conversation::class, ConversationPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
    }
}
