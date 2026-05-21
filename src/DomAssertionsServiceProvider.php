<?php

namespace Sinnbeck\DomAssertions;

use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestComponent;
use Illuminate\Testing\TestResponse;
use Illuminate\Testing\TestView;

class DomAssertionsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/dom-assertions.php', 'dom-assertions');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/dom-assertions.php' => $this->app->configPath('dom-assertions.php'),
        ], 'dom-assertions-config');

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
