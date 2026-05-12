<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            // Disable query log in production to reduce memory overhead
            DB::disableQueryLog();
        }

        // Surface N+1 issues immediately in development instead of silently degrading
        Model::preventLazyLoading(! $this->app->isProduction());

        Paginator::useBootstrapFive();
    }
}
