<?php

namespace Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \JoeSu\LaravelScaffold\Providers\LaravelScaffoldServiceProvider::class,
        ];
    }
}
