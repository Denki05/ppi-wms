<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Di balik Cloudflare, request ke origin bisa terbaca http sehingga
        // route() menghasilkan URL http (mixed content). Ikuti APP_URL.
        $appUrl = config('app.url');
        if (is_string($appUrl) && stripos($appUrl, 'https://') === 0) {
            URL::forceScheme('https');
        }
    }
}
