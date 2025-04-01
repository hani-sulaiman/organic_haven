<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\InteractionLogger;

class InteractionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(InteractionLogger::class, function ($app) {
            return new InteractionLogger();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // You can perform any bootstrapping here if needed
    }
}
