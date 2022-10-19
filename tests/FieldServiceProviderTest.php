<?php

declare(strict_types=1);

use Ardenthq\EnhancedMarkdown\FieldServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

it('adds the enhanced-markdown closure when serving nova', function () {
    Event::spy();

    $provider = new FieldServiceProvider(app());

    $provider->boot();

    Event::shouldHaveReceived('listen')->once()->with(ServingNova::class, Mockery::type('callable'));
});

it('adds the scripts when nova is serving', function () {
    Event::dispatch(new ServingNova(request()));

    expect(Nova::$scripts)->toHaveLength(1);
    expect(Nova::$scripts[0]->name())->toBe('enhanced-markdown');
});

it('registers the routes', function () {
    $routes = Route::getRoutes();
    expect($routes)->toHaveCount(1);
    expect($routes->getRoutes()[0]->uri)->toBe('ardenthq/nova-enhanced-markdown/{resource}/store/{field}');
});
