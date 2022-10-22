<?php

use PHPUnit\Framework\AssertionFailedError;
use Sinnbeck\DomAssertions\Asserts\ElementAssert;

it('can handle an empty view', function () {
    $this->get('empty')
        ->assertElement();
})->throws(
    AssertionFailedError::class,
    'The view is empty!'
);

it('can handle an empty body', function () {
    $this->get('empty-body')
        ->assertElement();
})->throws(
    AssertionFailedError::class,
    'No body element found!'
);

it('can parse broken html', function () {
    $this->get('broken')
        ->assertElement(function ($d) {
            $d->dd();
        });
});

it('can find the an element', function () {
    $this->get('nesting')
        ->assertElement();
});

it('can find the body', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $assert) {
            $assert->is('body');
        });
});

it('can find an element by selector', function () {
    $this->get('nesting')
        ->assertElement('#nav');
});

it('can fail finding anything', function () {
    $this->get('nesting')
        ->assertElement('div > nav');
})->throws(
    AssertionFailedError::class,
    'No element found with selector: div > nav'
);

it('can check the element has the correct type', function () {
    $this->get('nesting')
        ->assertElement('#nav', function (ElementAssert $element) {
            $element->is('nav');
        });
});

it('can fail matching element type', function () {
    $this->get('nesting')
        ->assertElement('#nav', function (ElementAssert $element) {
            $element->is('div');
        });
})->throws(
    AssertionFailedError::class,
    'Element is not of type "div"'
);

it('can fail with wrong type of selector', function () {
    $this->get('form')
        ->assertElement(['div']);
})->throws(AssertionFailedError::class, 'Invalid selector!');

it('can find a nested element', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->containsDiv();
        });
});

it('can find a nested element with content', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->contains('div', [
                'class' => 'foobar',
            ]);
        });
});

it('can find assert a class works no matter the order', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->contains('span', [
                'class' => 'foo bar',
            ]);
            $element->find('span', function (ElementAssert $span) {
                $span->has('class', 'foo bar');
            });
        });
});

it('can find multiple identical items', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->contains('div', [], 4);
        });
});

it('can find multiple identical items simplified', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->contains('div', 4);
        });
});

it('can find multiple identical items with content', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->contains('ul > li', [
                'x-data' => 'foobar',
            ], 2);
        });
});

it('can find multiple identical items with content ensuring no wrong matches', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->contains('div', [
                'x-data' => 'foobar',
            ], 1);
        });
});

it('can fail finding a nested element with content', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->contains('div', [
                'class' => 'foo',
            ]);
        });
})->throws(AssertionFailedError::class, 'Could not find a matching "div" with data:');

it('can find a nested element with content functional', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->findDiv(function (ElementAssert $element) {
                $element->is('div');
            });
        });
});

it('can find a nested element multiple levels', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->findDiv(function (ElementAssert $element) {
                $element->is('div');
                $element->find('div', function (ElementAssert $element) {
                    $element->is('div');
                    $element->findDiv(function (ElementAssert $element) {
                        $element->is('div');
                    });
                });
            });
        });
});

it('can find a nested element multiple levels by query', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->findDiv(function (ElementAssert $element) {
                $element->is('div');
                $element->find('.deep', function (ElementAssert $element) {
                    $element->is('div');
                    $element->findSpan(function (ElementAssert $element) {
                        $element->is('span');
                    });
                });
            });
        });
});

it('can find a nested element multiple levels by query and attributes', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->findDiv(function (ElementAssert $element) {
                $element->is('div');
                $element->contains('.deep', [
                    'class' => 'deep',
                ]);
            });
        });
});

it('can find a nested element and ensure doesnt contain', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->findDiv(function (ElementAssert $element) {
                $element->is('div');
                $element->doesntContain('nav');
            });
        });
});
