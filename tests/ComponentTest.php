<?php

use PHPUnit\Framework\AssertionFailedError;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use Tests\Views\Components\BrokenComponent;
use Tests\Views\Components\EmptyComponent;
use Tests\Views\Components\EmptyElementComponent;

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
