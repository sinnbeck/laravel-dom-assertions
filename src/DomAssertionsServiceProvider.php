<?php

namespace Sinnbeck\DomAssertions;

use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;

class DomAssertionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningUnitTests()) {
            TestResponse::mixin(new TestResponseMacros());
        }
    }
}
