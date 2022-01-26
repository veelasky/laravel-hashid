<?php

namespace Tests;

use Veelasky\LaravelHashId\HashIdServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    /** {@inheritdoc} */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /** {@inheritdoc} */
    protected function getPackageProviders($app)
    {
        return [HashIdServiceProvider::class];
    }
}
