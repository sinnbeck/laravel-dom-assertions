<?php

use Illuminate\Support\Traits\Macroable;
use Illuminate\Testing\TestComponent;
use PHPUnit\Framework\AssertionFailedError;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use Tests\Views\Components\BrokenComponent;
use Tests\Views\Components\EmptyBodyComponent;
use Tests\Views\Components\EmptyComponent;
use Tests\Views\Components\Html5Component;
use Tests\Views\Components\LivewireComponent;
use Tests\Views\Components\NestedComponent;

$testComponentMacroable = in_array(Macroable::class, class_uses(TestComponent::class) ?? []);
$testComponentMacroableMsg = 'Testing Blade components is unavailable in this version of Laravel';

it('can handle an empty component', function () {
    $this->component(EmptyComponent::class)
        ->assertElementExists();
})->throws(
    AssertionFailedError::class,
    'The component is empty!'
)->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can handle an empty body', function () {
    $this->component(EmptyBodyComponent::class)
        ->assertElementExists();
})->throws(
    AssertionFailedError::class,
    'No element found with selector: body'
)->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can parse broken html', function () {
    $this->component(BrokenComponent::class)
        ->assertElementExists();
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can find the element', function () {
    $this->component(NestedComponent::class)
        ->assertElementExists();
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can find the body', function () {
    $this->component(NestedComponent::class)
        ->assertElementExists('body', function (AssertElement $assert) {
            $assert->is('body');
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can check for html5', function () {
    $this->component(Html5Component::class)
        ->assertHtml5();
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can fail checking for html5', function () {
    $this->component(NestedComponent::class)
        ->assertHtml5();
})->throws(
    AssertionFailedError::class,
    'Not a html5 doctype!'
)->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can fail finding a class', function () {
    $this->component(Html5Component::class)
        ->assertElementExists('body', function (AssertElement $assert) {
            $assert->find('#nav', function (AssertElement $element) {
                $element->doesntHave('class');
            });
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can fail finding a href with exact match', function () {
    $this->component(Html5Component::class)
        ->assertElementExists('body', function (AssertElement $assert) {
            $assert->find('#nav a', function (AssertElement $element) {
                $element->has('href')
                    ->doesntHave('href', '/bar');
            });
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can fail when finding a id that isnt expected', function () {
    $this->component(Html5Component::class)
        ->assertElementExists('body', function (AssertElement $assert) {
            $assert->find('#nav', function (AssertElement $element) {
                $element->doesntHave('id');
            });
        });
})->throws(
    AssertionFailedError::class,
    'Found an attribute "id"'
)->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can fail when finding a href with matching value that isnt expected', function () {
    $this->component(Html5Component::class)
        ->assertElementExists('body', function (AssertElement $assert) {
            $assert->find('#nav a', function (AssertElement $element) {
                $element->doesntHave('href', '/foo');
            });
        });
})->throws(
    AssertionFailedError::class,
    'Found an attribute "href" with value "/foo"'
)->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can find an element by selector', function () {
    $this->component(Html5Component::class)
        ->assertElementExists('#nav');
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can fail finding anything', function () {
    $this->component(Html5Component::class)
        ->assertElementExists('div > nav');
})->throws(
    AssertionFailedError::class,
    'No element found with selector: div > nav'
)->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can check the element has the correct type', function () {
    $this->component(Html5Component::class)
        ->assertElementExists('#nav', function (AssertElement $element) {
            $element->is('nav');
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can fail matching element type', function () {
    $this->component(Html5Component::class)
        ->assertElementExists('#nav', function (AssertElement $element) {
            $element->is('div');
        });
})->throws(
    AssertionFailedError::class,
    'Element is not of type "div"'
)->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can fail with wrong type of selector', function () {
    $this->view('form')
        ->assertElementExists(['div']);
})->throws(
    AssertionFailedError::class,
    'Invalid selector!'
)->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can find a nested element', function () {
    $this->component(Html5Component::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->containsDiv();
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can find a nested element with content', function () {
    $this->component(Html5Component::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('div', [
                'class' => 'foobar',
            ]);
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can match text content', function () {
    $this->component(Html5Component::class)
        ->assertElementExists('span.bar', function (AssertElement $element) {
            $element->has('text', 'Foo');
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can match text content with duplicate spaces and vertical whitespace', function () {
    $this->component(Html5Component::class)
        ->assertElementExists('p.foo.bar', function (AssertElement $element) {
            $element->has('text', 'Foo Bar');
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can match text content containing a string', function () {
    $this->component(Html5Component::class)
        ->assertElementExists('p.foo.bar', function (AssertElement $element) {
            $element->containsText('Bar');
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can match text content containing a string ignoring case', function () {
    $this->component(Html5Component::class)
        ->assertElementExists('p.foo.bar', function (AssertElement $element) {
            $element->containsText('bar', true);
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can match text content not containing a string', function () {
    $this->component(Html5Component::class)
        ->assertElementExists('p.foo.bar', function (AssertElement $element) {
            $element->doesntContainText('bar');
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can match a class no matter the order', function () {
    $this->component(Html5Component::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('span', [
                'class' => 'foo bar',
            ]);
            $element->find('span', function (AssertElement $span) {
                $span->has('class', 'foo bar');
            });
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can match a partial class', function () {
    $this->component(Html5Component::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('span', [
                'class' => 'foo bar',
            ]);
            $element->find('span', function (AssertElement $span) {
                $span->has('class', 'bar');
            });
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can find multiple identical items', function () {
    $this->component(Html5Component::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('div', [], 4);
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can find multiple identical items simplified', function () {
    $this->component(Html5Component::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('div', 4);
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can find multiple identical items with content', function () {
    $this->component(Html5Component::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('ul > li', [
                'x-data' => 'foobar',
            ], 2);
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can find multiple identical items with content ensuring no wrong matches', function () {
    $this->component(Html5Component::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('div', [
                'x-data' => 'foobar',
            ], 1);
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can fail finding a nested element with content', function () {
    $this->component(Html5Component::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('div', [
                'class' => 'foo',
            ]);
        });
})->throws(
    AssertionFailedError::class,
    'Could not find a matching "div" with data:'
)->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can find a nested element with content functional', function () {
    $this->component(Html5Component::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->findDiv(function (AssertElement $element) {
                $element->is('div');
            });
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can find a nested element multiple levels', function () {
    $this->component(Html5Component::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->findDiv(function (AssertElement $element) {
                $element->is('div');
                $element->find('div', function (AssertElement $element) {
                    $element->is('div');
                    $element->findDiv(function (AssertElement $element) {
                        $element->is('div');
                    });
                });
            });
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can find a nested element multiple levels by query', function () {
    $this->component(Html5Component::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->findDiv(function (AssertElement $element) {
                $element->is('div');
                $element->find('.deep', function (AssertElement $element) {
                    $element->is('div');
                    $element->findSpan(function (AssertElement $element) {
                        $element->is('span');
                    });
                });
            });
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can find a nested element multiple levels by query and attributes', function () {
    $this->component(Html5Component::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->findDiv(function (AssertElement $element) {
                $element->is('div');
                $element->contains('.deep', [
                    'class' => 'deep',
                ]);
            });
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can find a nested element and ensure doesnt contain', function () {
    $this->component(Html5Component::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->findDiv(function (AssertElement $element) {
                $element->is('div');
                $element->doesntContain('nav');
            });
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can fail finding an contained element', function () {
    $this->component(Html5Component::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->findDiv(function (AssertElement $element) {
                $element->doesntContain('div');
            });
        });
})->throws(
    AssertionFailedError::class,
    'Found a matching element of type "div'
)->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can fail finding an contained element with query', function () {
    $this->component(Html5Component::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->findDiv(function (AssertElement $element) {
                $element->doesntContain('div.foobar');
            });
        });
})->throws(
    AssertionFailedError::class,
    'Found a matching element of type "div'
)->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can match on livewire attributes', function () {
    $this->component(LivewireComponent::class)
        ->assertElementExists('[wire\:model="foo"]', function (AssertElement $element) {
            $element->is('input');
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can match has on livewire attributes', function () {
    $this->component(LivewireComponent::class)
        ->assertElementExists('input', function (AssertElement $element) {
            $element->has('wire:model', 'foo');
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can match on livewire with contains', function () {
    $this->component(LivewireComponent::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('input[wire\:model="foo"]');
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);

it('can match on livewire contains as attribute', function () {
    $this->component(LivewireComponent::class)
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('input', [
                'wire:model' => 'foo',
            ]);
        });
})->skip(! $testComponentMacroable, $testComponentMacroableMsg);
