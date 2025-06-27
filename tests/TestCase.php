<?php

namespace JoeSu\LaravelScaffold\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use JoeSu\LaravelScaffold\Providers\LaravelScaffoldServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelScaffoldServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Set up test environment
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
