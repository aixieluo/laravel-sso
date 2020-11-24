<?php

namespace Aixieluo\LaravelSso;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SsoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerRoutes();
    }

    public function register()
    {
        $this->configure();
        $this->offerPublishing();
    }

    /**
     * Configure the service provider
     *
     * @return void
     */
    private function configure()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/sso.php', 'sso');
    }

    /**
     * Offer publishing for the service provider
     *
     * @return void
     */
    public function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/sso.php' => config_path('sso.php'),
            ], 'sso_config');
        }
    }

    /**
     * Register routes for the service provider
     *
     * @return void
     */
    private function registerRoutes()
    {
        Route::prefix('oauth')
            ->namespace('Aixieluo\LaravelSso\Http\Controllers')
            ->middleware('web')->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });
        Route::prefix('api/oauth')
            ->namespace('Aixieluo\LaravelSso\Http\Controllers')
            ->middleware('api')->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
            });
    }
}
