<?php

use Illuminate\Testing\TestComponent;
use Illuminate\Testing\TestResponse;
use Illuminate\Testing\TestView;
use Sinnbeck\DomAssertions\DomAssertionsServiceProvider;
use Sinnbeck\DomAssertions\TestHtml;

afterEach(function (): void {
    $this->app['env'] = 'testing';
    (new DomAssertionsServiceProvider($this->app))->boot();
});

it('registers the macros when not running unit tests', function (): void {
    TestResponse::flushMacros();
    TestView::flushMacros();
    TestHtml::flushMacros();
    TestComponent::flushMacros();

    $this->app['env'] = 'local';
    expect($this->app->runningUnitTests())->toBeFalse();

    (new DomAssertionsServiceProvider($this->app))->boot();

    expect(TestResponse::hasMacro('assertElement'))->toBeTrue()
        ->and(TestView::hasMacro('assertElement'))->toBeTrue()
        ->and(TestHtml::hasMacro('assertElement'))->toBeTrue()
        ->and(TestComponent::hasMacro('assertElement'))->toBeTrue();
});
