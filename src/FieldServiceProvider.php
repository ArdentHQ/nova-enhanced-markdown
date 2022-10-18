<?php

declare(strict_types=1);

namespace Ardenthq\EnhancedMarkdown;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Illuminate\Foundation\Application;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(static function (ServingNova $event) {
            Nova::script('enhanced-markdown', __DIR__.'/../dist/js/field.js');
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        /** @var Application $app */
        $app = $this->app;

        if ($app->routesAreCached()) {
            return;
        }

        Route::middleware('nova')
            ->prefix('ardenthq/nova-enhanced-markdown')
            ->group(__DIR__.'/../routes/api.php');
    }

}
