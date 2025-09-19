<?php

namespace Sinnbeck\DomAssertions;

use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestComponent;
use Illuminate\Testing\TestResponse;
use Illuminate\Testing\TestView;

class DomAssertionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningUnitTests()) {
            TestResponse::mixin(new TestResponseMacros);
            TestView::mixin(new TestViewMacros);
            if (version_compare($this->app->version(), '11.41.0', '>=')) {
                // @phpstan-ignore-next-line
                TestComponent::mixin(new TestComponentMacros);
            }
        }
    }
}
