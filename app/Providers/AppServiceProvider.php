<?php

namespace App\Providers;

use App\Services\SettingsService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register SettingsService as a singleton
        $this->app->singleton('settings', function () {
            return new SettingsService();
        });

        // Also bind to SettingsService for dependency injection
        $this->app->bind(SettingsService::class, function () {
            return new SettingsService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production (Vercel, staging)
        if ($this->app->environment(['production', 'staging'])) {
            URL::forceScheme('https');
        }

        // Vercel memakai filesystem sementara; pastikan Blade punya path valid saat runtime.
        $compiledPath = env('VIEW_COMPILED_PATH', storage_path('framework/views'));

        if (! is_dir($compiledPath)) {
            mkdir($compiledPath, 0777, true);
        }

        config(['view.compiled' => $compiledPath]);
    }
}
