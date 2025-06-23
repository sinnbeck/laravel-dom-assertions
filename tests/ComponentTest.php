<?php

use PHPUnit\Framework\AssertionFailedError;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use Tests\Views\Components\BrokenComponent;
use Tests\Views\Components\EmptyComponent;
use Tests\Views\Components\EmptyElementComponent;
use Tests\Views\Components\Html5Component;
use Tests\Views\Components\NestedComponent;

it('can handle an empty component', function () {
    $this->component(EmptyComponent::class)
        ->assertElementExists();
})->throws(
    AssertionFailedError::class,
    'The component is empty!'
);

it('can handle an empty element', function () {
    $this->component(EmptyElementComponent::class)
        ->assertElementExists('main');
})->throws(
    AssertionFailedError::class,
    'No element found with selector: main'
);

it('can parse broken html', function () {
    $this->component(BrokenComponent::class)
        ->assertElementExists();
});

it('can find a nested element', function () {
    $this->component(NestedComponent::class)
        ->assertElementExists('section');
});

it('can find the section', function () {
    $this->component(NestedComponent::class)
        ->assertElementExists('section', function (AssertElement $assert) {
            $assert->is('section');
        });
});

it('can check for html5', function () {
    $this->component(Html5Component::class)
        ->assertHtml5();
});

it('can fail checking for html5', function () {
    $this->component(NestedComponent::class)
        ->assertHtml5();
})->throws(
    AssertionFailedError::class,
    'Not a html5 doctype!'
);
