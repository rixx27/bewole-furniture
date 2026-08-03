<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\ProductReview;
use App\Models\WebsiteSetting;
use App\Policies\OrderPolicy;
use App\Policies\ProductReviewPolicy;
use App\Policies\WebsiteSettingPolicy;
use App\Repositories\WebsiteSettingRepository;
use App\Repositories\WebsiteSettingRepositoryInterface;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerRepositories();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerPolicies();
    }

    /**
     * Register application policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(ProductReview::class, ProductReviewPolicy::class);
        Gate::policy(WebsiteSetting::class, WebsiteSettingPolicy::class);
    }

    /**
     * Register repositories.
     */
    protected function registerRepositories(): void
    {
        $this->app->bind(WebsiteSettingRepositoryInterface::class, WebsiteSettingRepository::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
