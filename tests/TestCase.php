<?php

declare(strict_types=1);

namespace Tests;

use Ardenthq\EnhancedMarkdown\FieldServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Laravel\Nova\Nova;
use Orchestra\Testbench\TestCase as Orchestra;
use Tests\fixtures\ExampleResource;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);

        $this->bootServideProvider($this->app);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('example_model', function (Blueprint $table) {
            $table->increments('id');
            $table->string('content');
            $table->timestamps();
        });
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function bootServideProvider($app)
    {
        (new FieldServiceProvider($app))->boot();
    }
}
