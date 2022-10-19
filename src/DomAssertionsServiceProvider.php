<?php

namespace Sinnbeck\DomAssertions;

use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;
use Sinnbeck\DomAssertions\Macros\AssertElementMacro;
use Sinnbeck\DomAssertions\Macros\AssertFormMacro;

class DomAssertionsServiceProvider extends ServiceProvider
{
    public function register()
    {
        TestResponse::macro('assertForm', app()->call(AssertFormMacro::class));
        TestResponse::macro('assertElement', app()->call(AssertElementMacro::class));
    }
}
