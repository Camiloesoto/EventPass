<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Gate::define('access-admin', function ($user) {
            $emails = (array) (config('services.admin.emails') ?? []);
            // Normalise and compare strictly
            $emails = array_values(array_filter(array_map(fn ($e) => is_string($e) ? trim(strtolower($e)) : null, $emails)));
            return in_array(strtolower((string) $user->email), $emails, true);
        });
    }
}
