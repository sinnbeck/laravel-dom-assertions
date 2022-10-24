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
        Route::view('nesting', 'nesting');
        Route::view('empty', 'empty');
        Route::view('empty-body', 'empty-body');
        Route::view('broken', 'broken');
        Route::view('livewire', 'livewire');
        Route::get('about', function () {
            $menuItems = [
                [
                    'route' => 'home',
                    'name' => 'Home',
                ],
                [
                    'route' => 'about',
                    'name' => 'About',
                ],
                [
                    'route' => 'links',
                    'name' => 'Links',
                ],
            ];

            return view('nav-example', ['menuItems' => $menuItems]);
        })->name('about');
        Route::view('home', 'broken')->name('home');
        Route::view('links', 'broken')->name('links');
    }
}
