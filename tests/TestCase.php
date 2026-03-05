<?php

namespace Tests;

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Support\Facades\Route;
use Livewire\LivewireServiceProvider;
use Sinnbeck\DomAssertions\DomAssertionsServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use InteractsWithViews;

    protected function getPackageProviders($app)
    {
        return [
            DomAssertionsServiceProvider::class,
            LivewireServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('view.paths', [__DIR__.'/views']);
        $app['config']->set('app.key', 'base64:9CpCKisSUj8BrPJu2LeKXMyFi4a0/U/4Cb7/8K558w4=');
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
