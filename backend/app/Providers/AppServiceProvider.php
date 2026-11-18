<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Dimension;
use App\Models\Indicador;
use App\Models\Item;
use App\Models\User;
use App\Models\Evaluacion;
use App\Models\EvaluacionItem;
use App\Observers\AuditoriaObserver;


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
        Dimension::observe(AuditoriaObserver::class);
        Indicador::observe(AuditoriaObserver::class);
        Item::observe(AuditoriaObserver::class);
        User::observe(AuditoriaObserver::class);
        Evaluacion::observe(AuditoriaObserver::class);
        EvaluacionItem::observe(AuditoriaObserver::class);
    }
}
