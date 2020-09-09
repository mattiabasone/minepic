<?php

declare(strict_types=1);

namespace Minepic\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('APP_FORCE_HTTPS')) {
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
    }
}
