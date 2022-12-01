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
            Nova::style('enhanced-markdown', __DIR__.'/../dist/css/field.css');
        });
    }

    /**
     * Register the field routes.
     *
     * @return void
     */
    protected function routes()
    {
        Route::domain(config('nova.domain', null))
            ->middleware(config('nova.middleware', []))
            ->prefix('ardenthq/nova-enhanced-markdown')
            ->group(__DIR__.'/../routes/api.php');
    }
}
