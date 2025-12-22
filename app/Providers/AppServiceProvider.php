<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\View\Composers\CategoryComposer;
use App\View\Composers\SettingComposer;

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
        // Partager les catégories avec toutes les vues
        View::composer('layouts.app', CategoryComposer::class);
        
        // Partager les paramètres du site avec toutes les vues
        View::composer('*', SettingComposer::class);
    }
}
