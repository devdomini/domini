<?php

namespace App\Providers;

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
    public function boot(): void
    {
        // Configuration de la locale française pour Carbon
        \Carbon\Carbon::setLocale('fr');
        setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fra');
        
        // Configuration de la pagination par défaut
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.domini');
        \Illuminate\Pagination\Paginator::defaultSimpleView('vendor.pagination.domini');
    }
}
