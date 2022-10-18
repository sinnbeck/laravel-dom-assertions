<?php

namespace Tests;

use Illuminate\Support\Facades\Route;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Sinnbeck\DomAssertions\DomAssertionsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('view.paths', [__DIR__.'/views']);
    }

    protected function defineRoutes($router)
    {
        Route::view('form', 'form');
    }
}
