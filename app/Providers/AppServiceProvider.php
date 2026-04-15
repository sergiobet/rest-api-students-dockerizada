<?php

namespace App\Providers;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

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
    public function boot(UrlGenerator $url): void
    {
        /**  
            * * Esto es necesario para el despliege en Render, usando el plan gratuito
        */
        
        //Si la aplicación está en producción, forzamos que todas las URLs generadas sean HTTPS
        if (app()->isProduction()) {
            $url->forceScheme('https');
        }
    }
}
