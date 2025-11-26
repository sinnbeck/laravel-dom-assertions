<?php

use PHPUnit\Framework\AssertionFailedError;
use Sinnbeck\DomAssertions\Asserts\AssertElement;

it('assertAlias alias works for assertElementExists', function () {
    $this->get('nesting')
        ->assertElementExists('body', function (AssertElement $assert) {
            $assert->is('body');
        });
});

it('assertDoesntExist works as expected', function () {
    $this->get('nesting')
        ->assertDoesntExist('span.fake')
        ->assertDoesntExist('nav.fake');
});

it('assertContainsElement works as expected', function () {
    $this->get('nesting')
        ->assertContainsElement('span.foo', ['text' => 'Foo', 'class' => 'bar foo'])
        ->assertContainsElement('nav');
});

it('assertContainsElement throws if selector not found', function () {
    $this->get('nesting')
        ->assertContainsElement('span.non-existing', ['text' => 'Foo']);
})->throws(AssertionFailedError::class, 'No element found with selector: span.non-existing');

it('assertContainsElement throws if contains text does not exist', function () {
    $this->get('nesting')
        ->assertContainsElement('span.foo', ['text' => 'non-existing']);
})->throws(AssertionFailedError::class, 'Failed asserting that element [span.foo] text contains "non-existing". Actual: "Foo"');

it('assertContainsElement throws if contains attribute does not exist', function () {
    $this->view('nesting')
        ->assertContainsElement('span.foo', ['non-existing-attribute' => 'non-existing']);
})->throws(AssertionFailedError::class, 'Attribute [non-existing-attribute] not found in element [span.foo]');

it('can handle an empty view', function () {
    $this->get('empty')
        ->assertElementExists();
})->throws(
    AssertionFailedError::class,
    'The view is empty!'
);

it('can handle an empty body', function () {
    $this->get('empty-body')
        ->assertElementExists();
})->throws(
    AssertionFailedError::class,
    'No element found with selector: body'
);

it('can parse broken html', function () {
    $this->get('broken')
        ->assertElementExists();
});

it('can find the an element', function () {
    $this->get('nesting')
        ->assertElementExists();
});

it('can find the body', function () {
    $this->get('nesting')
        ->assertElementExists('body', function (AssertElement $assert) {
            $assert->is('body');
        });
});

it('can check for html5', function () {
    $this->get('nesting')
        ->assertHtml5();
});

it('can fail checking for html5', function () {
    $this->get('livewire')
        ->assertHtml5();
})->throws(
    AssertionFailedError::class,
    'Not a html5 doctype!'
);

it('can find the head', function () {
    $this->get('nesting')
        ->assertElementExists('head', function (AssertElement $assert) {
            $assert->is('head');
        });
});

it('can find a meta tag', function () {
    $this->get('nesting')
        ->assertElementExists('head', function (AssertElement $assert) {
            $assert->contains('meta', [
                'charset' => 'UTF-8',
            ]);
        });
});

it('can find a title', function () {
    $this->get('nesting')
        ->assertElementExists('head', function (AssertElement $assert) {
            $assert->find('title', function (AssertElement $element) {
                $element->has('text', 'Nesting');
            });
        });
});

it('can fail finding a class', function () {
    $this->get('nesting')
        ->assertElementExists('body', function (AssertElement $assert) {
            $assert->find('#nav', function (AssertElement $element) {
                $element->doesntHave('class');
            });
        });
});

it('can fail finding a href with exact match', function () {
    $this->get('nesting')
        ->assertElementExists('body', function (AssertElement $assert) {
            $assert->find('#nav a', function (AssertElement $element) {
                $element->has('href')
                    ->doesntHave('href', '/bar');
            });
        });
});

it('can fail when finding a id that isnt expected', function () {
    $this->get('nesting')
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
    $this->get('nesting')
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
    $this->get('nesting')
        ->assertElementExists('#nav');
});

it('can fail finding anything', function () {
    $this->get('nesting')
        ->assertElementExists('div > nav');
})->throws(
    AssertionFailedError::class,
    'No element found with selector: div > nav'
);

it('can check the element has the correct type', function () {
    $this->get('nesting')
        ->assertElementExists('#nav', function (AssertElement $element) {
            $element->is('nav');
        });
});

it('can fail matching element type', function () {
    $this->get('nesting')
        ->assertElementExists('#nav', function (AssertElement $element) {
            $element->is('div');
        });
})->throws(
    AssertionFailedError::class,
    'Element is not of type "div"'
);

it('can fail with wrong type of selector', function () {
    $this->get('form')
        ->assertElementExists(['div']);
})->throws(AssertionFailedError::class, 'Invalid selector!');

it('can find a nested element', function () {
    $this->get('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->containsDiv();
        });
});

it('can find a nested element with content', function () {
    $this->get('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('div', [
                'class' => 'foobar',
            ]);
        });
});

it('can match text content', function () {
    $this->get('nesting')
        ->assertElementExists('span.bar', function (AssertElement $element) {
            $element->has('text', 'Foo');
        });
});

it('can match text content with duplicate spaces and vertical whitespace', function () {
    $this->get('nesting')
        ->assertElementExists('p.foo.bar', function (AssertElement $element) {
            $element->has('text', 'Foo Bar');
        });
});

it('can match text content containing a string', function () {
    $this->get('nesting')
        ->assertElementExists('p.foo.bar', function (AssertElement $element) {
            $element->containsText('Bar');
        });
});

it('can match text content containing a string ignoring case', function () {
    $this->get('nesting')
        ->assertElementExists('p.foo.bar', function (AssertElement $element) {
            $element->containsText('bar', true);
        });
});

it('can match text content not containing a string', function () {
    $this->get('nesting')
        ->assertElementExists('p.foo.bar', function (AssertElement $element) {
            $element->doesntContainText('bar');
        });
});

it('can match a class no matter the order', function () {
    $this->get('nesting')
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
    $this->get('nesting')
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
    $this->get('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('div', [], 4);
        });
});

it('can find multiple identical items simplified', function () {
    $this->get('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('div', 4);
        });
});

it('can find multiple identical items with content', function () {
    $this->get('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('ul > li', [
                'x-data' => 'foobar',
            ], 2);
        });
});

it('can find multiple identical items with content ensuring no wrong matches', function () {
    $this->get('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('div', [
                'x-data' => 'foobar',
            ], 1);
        });
});

it('can fail finding a nested element with content', function () {
    $this->get('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('div', [
                'class' => 'foo',
            ]);
        });
})->throws(AssertionFailedError::class, 'Could not find a matching "div" with data:');

it('can run assertions against all elements that match the selection', function () {
    $this->get('form')
        ->assertOk()
        ->assertElementExists(fn (AssertElement $view) => $view
            ->each('select', fn (AssertElement $select) => $select->has('name'))
        );
});

it('can run assertions against indexed elements that match the selection', function () {
    $this->get('form')
        ->assertOk()
        ->assertElementExists(function (AssertElement $view) {
            $all = $view->getParser()->queryAll('select');
            $view->each('select', fn (AssertElement $select, int $index) => $select->containsText($all->item($index)->nodeValue));
        });
});

it('fails when each() is used but no elements match the selector', function () {
    $this->get('form')
        ->assertOk()
        ->assertElementExists(fn (AssertElement $view) => $view
            ->each('img', fn (AssertElement $image) => $image->has('alt'))
        );
})->throws(AssertionFailedError::class);

it('can find a nested element with content functional', function () {
    $this->get('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->findDiv(function (AssertElement $element) {
                $element->is('div');
            });
        });
});

it('can find a nested element multiple levels', function () {
    $this->get('nesting')
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
    $this->get('nesting')
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
    $this->get('nesting')
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
    $this->get('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->findDiv(function (AssertElement $element) {
                $element->is('div');
                $element->doesntContain('nav');
            });
        });
});

it('can fail finding an contained element', function () {
    $this->get('nesting')
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
    $this->get('nesting')
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
    $this->get('livewire')
        ->assertOk()
        ->assertElementExists('[wire\:model="foo"]', function (AssertElement $element) {
            $element->is('input');
        });
});

it('can match has on livewire attributes', function () {
    $this->get('livewire')
        ->assertOk()
        ->assertElementExists('input', function (AssertElement $element) {
            $element->has('wire:model', 'foo');
        });
});

it('can match on livewire with contains', function () {
    $this->get('livewire')
        ->assertOk()
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('input[wire\:model="foo"]');
        });
});

it('can match on livewire contains as attribute', function () {
    $this->get('livewire')
        ->assertOk()
        ->assertElementExists(function (AssertElement $element) {
            $element->contains('input', [
                'wire:model' => 'foo',
            ]);
        });
});

it('includes dom selectors when nesting and errors occur', function () {
    $this->get('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->findDiv(function (AssertElement $element) {
                $element->is('div');
                $element->find('div', function (AssertElement $element) {
                    $element->is('div');
                    $element->findDiv(function (AssertElement $element) {
                        $element->contains('not-existing');
                    });
                });
            });
        });
})->throws(AssertionFailedError::class, 'Could not find any matching element of type "not-existing" within: div > div > div');

it('includes dom selectors when only one deep', function () {
    $this->get('nesting')
        ->assertElementExists(function (AssertElement $element) {
            $element->find('.foobar', function (AssertElement $element) {
                $element->is('not-real');
            });
        });
})->throws(AssertionFailedError::class, 'Element is not of type "not-real" within: .foobar');

it('can run the example from the readme', function () {
    $this->get(route('about'))
        ->assertOk()
        ->assertElementExists('nav > ul', function (AssertElement $ul) {
            $ul->contains('li', [
                'class' => 'active',
                'text' => 'About',
            ]);
            $ul->doesntContain('li', [
                'class' => 'active',
                'text' => 'Home',
            ]);
        });
});
