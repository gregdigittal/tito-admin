<?php

namespace App\Providers;

use App\Services\TitoGraphQLService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TitoGraphQLService::class, fn () => new TitoGraphQLService());
    }

    public function boot(): void
    {
        //
    }
}
