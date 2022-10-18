<?php

declare(strict_types=1);

namespace Ardenthq\EnhancedMarkdown;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

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
     * Register the field routes.
     *
     * @return void
     */
    protected function routes()
    {
        Route::middleware('nova')
            ->prefix('ardenthq/nova-enhanced-markdown')
            ->group(__DIR__.'/../routes/api.php');
    }
}
