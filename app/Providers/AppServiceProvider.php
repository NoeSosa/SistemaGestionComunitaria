<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Spatie\Activitylog\Models\Activity;
use App\Observers\ActivityObserver;

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
        // Si la URL contiene 'ngrok', forzamos HTTPS
        if (str_contains(request()->url(), 'ngrok-free.app')) {
            URL::forceScheme('https');
        }

        Activity::observe(ActivityObserver::class);
    }
}
