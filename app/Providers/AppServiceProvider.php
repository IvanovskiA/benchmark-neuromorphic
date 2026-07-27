<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        URL::forceRootUrl(rtrim(config('app.url'), '/'));

        // Relative pagination paths so links keep the browser port (e.g. :8080).
        Paginator::currentPathResolver(function (): string {
            $path = request()->path();

            return $path === '' ? '/' : '/'.$path;
        });

        Vite::createAssetPathsUsing(fn (string $path) => '/'.ltrim($path, '/'));
    }
}
