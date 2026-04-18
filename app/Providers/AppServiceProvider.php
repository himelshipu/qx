<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Conversation;
use App\Models\Influencer;
use App\Models\Order;
use App\Models\Package;
use App\Models\Setting;
use App\Models\Role;
use App\Policies\CampaignPolicy;
use App\Policies\ConversationPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PackagePolicy;
use App\Policies\RolePolicy;
use App\Services\Frontend\CampaignNegotiationService;
use App\Services\Frontend\Contracts\CampaignNegotiationServiceInterface;
use App\View\Composers\FooterComposer;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            CampaignNegotiationServiceInterface::class,
            CampaignNegotiationService::class
        );
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

        $siteName = Setting::get('branding.site_name');
        if ($siteName) {
            config(['app.name' => $siteName]);
        }

        $mailerConfig = [
            'mail.default' => Setting::get('email.mailer', config('mail.default')),
            'mail.mailers.smtp.host' => Setting::get('email.host', config('mail.mailers.smtp.host')),
            'mail.mailers.smtp.port' => Setting::get('email.port', config('mail.mailers.smtp.port')),
            'mail.mailers.smtp.username' => Setting::get('email.username', config('mail.mailers.smtp.username')),
            'mail.mailers.smtp.password' => Setting::get('email.password', config('mail.mailers.smtp.password')),
            'mail.mailers.smtp.encryption' => Setting::get('email.encryption', config('mail.mailers.smtp.encryption')),
            'mail.from.address' => Setting::get('email.from_address', config('mail.from.address')),
            'mail.from.name' => Setting::get('email.from_name', config('mail.from.name')),
        ];

        config(array_filter($mailerConfig, fn ($value) => $value !== null && $value !== ''));

        // Register authorization policies
        Gate::policy(Campaign::class, CampaignPolicy::class);
        Gate::policy(Package::class, PackagePolicy::class);
        Gate::policy(Conversation::class, ConversationPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);

        // Register view composers
        View::composer('components.frontend.navigation.footer', FooterComposer::class);
    }
}
