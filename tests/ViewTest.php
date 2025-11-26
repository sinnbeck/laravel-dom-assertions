<?php

use PHPUnit\Framework\AssertionFailedError;
use Sinnbeck\DomAssertions\Asserts\AssertElement;

it('assertDoesntExist works as expected', function () {
    $this->get('nesting')
        ->assertDoesntExist('span.fake')
        ->assertDoesntExist('nav.fake');
});

it('assertContainsElement works as expected', function () {
    $this->view('nesting')
        ->assertContainsElement('span.foo', ['text' => 'Foo'])
        ->assertContainsElement('nav');
});

it('assertContainsElement throws if selector not found', function () {
    $this->view('nesting')
        ->assertContainsElement('span.non-existing', ['text' => 'Foo']);
})->throws(AssertionFailedError::class, 'No element found with selector: span.non-existing');

it('assertContainsElement throws if contains text does not exist', function () {
    $this->view('nesting')
        ->assertContainsElement('span.foo', ['text' => 'non-existing']);
})->throws(AssertionFailedError::class, 'Failed asserting that element [span.foo] text contains "non-existing". Actual: "Foo"');

it('assertContainsElement throws if contains attribute does not exist', function () {
    $this->view('nesting')
        ->assertContainsElement('span.foo', ['non-existing-attribute' => 'non-existing']);
})->throws(AssertionFailedError::class, 'Attribute [non-existing-attribute] not found in element [span.foo]');

it('assertElement alias works for assertElementExists', function () {
    $this->view('nesting')
        ->assertElement('body', function (AssertElement $assert) {
            $assert->is('body');
        });
});

it('can handle an empty view', function () {
    $this->view('empty')
        ->assertElementExists();
})->throws(
    AssertionFailedError::class,
    'The view is empty!'
);

it('can handle an empty body', function () {
    $this->view('empty-body')
        ->assertElementExists();
})->throws(
    AssertionFailedError::class,
    'No element found with selector: body'
);

it('can parse broken html', function () {
    $this->view('broken')
        ->assertElementExists();
});

it('can find the element', function () {
    $this->view('nesting')
        ->assertElementExists();
});

it('can find the body', function () {
    $this->view('nesting')
        ->assertElementExists('body', function (AssertElement $assert) {
            $assert->is('body');
        });
});

it('can check for html5', function () {
    $this->view('nesting')
        ->assertHtml5();
});

it('can fail checking for html5', function () {
    $this->view('livewire')
        ->assertHtml5();
})->throws(
    AssertionFailedError::class,
    'Not a html5 doctype!'
);

it('can find the head', function () {
    $this->view('nesting')
        ->assertElementExists('head', function (AssertElement $assert) {
            $assert->is('head');
        });
});

it('can find a meta tag', function () {
    $this->view('nesting')
        ->assertElementExists('head', function (AssertElement $assert) {
            $assert->contains('meta', [
                'charset' => 'UTF-8',
            ]);
        });
});

it('can find a title', function () {
    $this->view('nesting')
        ->assertElementExists('head', function (AssertElement $assert) {
            $assert->find('title', function (AssertElement $element) {
                $element->has('text', 'Nesting');
            });
        });
});

it('can fail finding a class', function () {
    $this->view('nesting')
        ->assertElementExists('body', function (AssertElement $assert) {
            $assert->find('#nav', function (AssertElement $element) {
                $element->doesntHave('class');
            });
        });
});

it('can fail finding a href with exact match', function () {
    $this->view('nesting')
        ->assertElementExists('body', function (AssertElement $assert) {
            $assert->find('#nav a', function (AssertElement $element) {
                $element->has('href')
                    ->doesntHave('href', '/bar');
            });
        });
});

it('can fail when finding a id that isnt expected', function () {
    $this->view('nesting')
        ->assertElementExists('body', function (AssertElement $assert) {
            $assert->find('#nav', function (AssertElement $element) {
                $element->doesntHave('id');
            });
        });
})->throws(
    AssertionFailedError::class,
    'Found an attribute "id"'
);

it('can fail when finding a href with matching value that isnt expected', function () {
    $this->view('nesting')
        ->assertElementExists('body', function (AssertElement $assert) {
            $assert->find('#nav a', function (AssertElement $element) {
                $element->doesntHave('href', '/foo');
            });
        });
})->throws(
    AssertionFailedError::class,
    'Found an attribute "href" with value "/foo"'
);

it('can find an element by selector', function () {
    $this->view('nesting')
        ->assertElementExists('#nav');
});

it('can fail finding anything', function () {
    $this->view('nesting')
        ->assertElementExists('div > nav');
})->throws(
    AssertionFailedError::class,
    'No element found with selector: div > nav'
);

it('can check the element has the correct type', function () {
    $this->view('nesting')
        ->assertElementExists('#nav', function (AssertElement $element) {
            $element->is('nav');
        });
});

it('can fail matching element type', function () {
    $this->view('nesting')
        ->assertElementExists('#nav', function (AssertElement $element) {
            $element->is('div');
        });
})->throws(
    AssertionFailedError::class,
    'Element is not of type "div"'
);

it('can fail with wrong type of selector', function () {
    $this->view('form')
        ->assertElementExists(['div']);
})->throws(AssertionFailedError::class, 'Invalid selector!');

it('can find a nested element', function () {
    $this->view('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->containsDiv();
        });
});

it('can find a nested element with content', function () {
    $this->view('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('div', [
                'class' => 'foobar',
            ]);
        });
});

it('can match text content', function () {
    $this->view('nesting')
        ->assertElementExists('span.bar', function (AssertElement $element) {
            $element->has('text', 'Foo');
        });
});

it('can match text content with duplicate spaces and vertical whitespace', function () {
    $this->view('nesting')
        ->assertElementExists('p.foo.bar', function (AssertElement $element) {
            $element->has('text', 'Foo Bar');
        });
});

it('can match text content containing a string', function () {
    $this->view('nesting')
        ->assertElementExists('p.foo.bar', function (AssertElement $element) {
            $element->containsText('Bar');
        });
});

it('can match text content containing a string ignoring case', function () {
    $this->view('nesting')
        ->assertElementExists('p.foo.bar', function (AssertElement $element) {
            $element->containsText('bar', true);
        });
});

it('can match text content not containing a string', function () {
    $this->view('nesting')
        ->assertElementExists('p.foo.bar', function (AssertElement $element) {
            $element->doesntContainText('bar');
        });
});

it('can match a class no matter the order', function () {
    $this->view('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('span', [
                'class' => 'foo bar',
            ]);
            $element->find('span', function (AssertElement $span) {
                $span->has('class', 'foo bar');
            });
        });
});

it('can match a partial class', function () {
    $this->view('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('span', [
                'class' => 'foo bar',
            ]);
            $element->find('span', function (AssertElement $span) {
                $span->has('class', 'bar');
            });
        });
});

it('can find multiple identical items', function () {
    $this->view('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('div', [], 4);
        });
});

it('can find multiple identical items simplified', function () {
    $this->view('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('div', 4);
        });
});

it('can find multiple identical items with content', function () {
    $this->view('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('ul > li', [
                'x-data' => 'foobar',
            ], 2);
        });
});

it('can find multiple identical items with content ensuring no wrong matches', function () {
    $this->view('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('div', [
                'x-data' => 'foobar',
            ], 1);
        });
});

it('can fail finding a nested element with content', function () {
    $this->view('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('div', [
                'class' => 'foo',
            ]);
        });
})->throws(AssertionFailedError::class, 'Could not find a matching "div" with data:');

it('can find a nested element with content functional', function () {
    $this->view('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->findDiv(function (AssertElement $element) {
                $element->is('div');
            });
        });
});

it('can find a nested element multiple levels', function () {
    $this->view('nesting')
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
});

it('can find a nested element multiple levels by query', function () {
    $this->view('nesting')
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
});

it('can find a nested element multiple levels by query and attributes', function () {
    $this->view('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->findDiv(function (AssertElement $element) {
                $element->is('div');
                $element->contains('.deep', [
                    'class' => 'deep',
                ]);
            });
        });
});

it('can find a nested element and ensure doesnt contain', function () {
    $this->view('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->findDiv(function (AssertElement $element) {
                $element->is('div');
                $element->doesntContain('nav');
            });
        });
});

it('can fail finding an contained element', function () {
    $this->view('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->findDiv(function (AssertElement $element) {
                $element->doesntContain('div');
            });
        });
})->throws(
    AssertionFailedError::class,
    'Found a matching element of type "div'
);

it('can fail finding an contained element with query', function () {
    $this->view('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->findDiv(function (AssertElement $element) {
                $element->doesntContain('div.foobar');
            });
        });
})->throws(
    AssertionFailedError::class,
    'Found a matching element of type "div'
);

it('can match on livewire attributes', function () {
    $this->view('livewire')
        ->assertElementExists('[wire\:model="foo"]', function (AssertElement $element) {
            $element->is('input');
        });
});

it('can match has on livewire attributes', function () {
    $this->view('livewire')
        ->assertElementExists('input', function (AssertElement $element) {
            $element->has('wire:model', 'foo');
        });
});

it('can match on livewire with contains', function () {
    $this->view('livewire')
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('input[wire\:model="foo"]');
        });
});

it('can match on livewire contains as attribute', function () {
    $this->view('livewire')
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('input', [
                'wire:model' => 'foo',
            ]);
        });
});
