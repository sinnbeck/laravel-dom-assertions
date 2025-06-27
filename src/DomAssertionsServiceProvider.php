<?php

namespace Sinnbeck\DomAssertions;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Macroable;
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

            // https://github.com/laravel/framework/pull/54359
            if (in_array(Macroable::class, class_uses(TestComponent::class) ?? [])) {
                TestComponent::mixin(new TestComponentMacros);
            }
        }
    }
}
