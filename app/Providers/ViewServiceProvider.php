<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share platform settings with all views (sidebar, header, etc.)
        View::composer('*', function ($view) {
            // Cache the platform settings for the entire request lifecycle
            static $platformBrand = null;
            static $platformLogo = null;
            static $loaded = false;

            if (!$loaded) {
                $platformBrand = \App\Models\System\PlatformSetting::where('key', 'brand_name')->value('value') ?? 'Platform';
                $platformLogo = \App\Models\System\PlatformSetting::where('key', 'logo_path')->value('value');
                $loaded = true;
            }

            $view->with('__platformBrand', $platformBrand);
            $view->with('__platformLogo', $platformLogo);
        });
    }
}
