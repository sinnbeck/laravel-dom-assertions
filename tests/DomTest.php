<?php

use PHPUnit\Framework\AssertionFailedError;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use Sinnbeck\DomAssertions\Asserts\BaseAssert;

it('assertAlias alias works for assertElementExists', function (): void {
    $this->get('nesting')
        ->assertElementExists('body', static function (AssertElement $assert): void {
            $assert->is('body');
        });
});

it('assertDoesntExist works as expected', function (): void {
    $this->get('nesting')
        ->assertDoesntExist('span.fake')
        ->assertDoesntExist('nav.fake');
});

it('assertContainsElement works as expected', function (): void {
    $this->get('nesting')
        ->assertContainsElement('span.foo', ['text' => 'Foo', 'class' => 'bar foo'])
        ->assertContainsElement('nav');
});

it('assertContainsElement throws if selector not found', function (): void {
    $this->get('nesting')
        ->assertContainsElement('span.non-existing', ['text' => 'Foo']);
})->throws(AssertionFailedError::class, 'No element found with selector: span.non-existing');

it('assertContainsElement throws if contains text does not exist', function (): void {
    $this->get('nesting')
        ->assertContainsElement('span.foo', ['text' => 'non-existing']);
})->throws(AssertionFailedError::class, 'Failed asserting that any element [span.foo] text contains "non-existing".');

it('assertContainsElement throws if contains attribute does not exist', function (): void {
    $this->view('nesting')
        ->assertContainsElement('span.foo', ['non-existing-attribute' => 'non-existing']);
})->throws(AssertionFailedError::class, 'Failed asserting that attribute [non-existing-attribute] of any element [span.foo] contains "non-existing".');

it('assertContainsElement matches text across child elements', function (): void {
    $this->get('nesting')
        ->assertContainsElement('p.foo', ['text' => 'Foo Bar']);
});

it('assertContainsElement matches text separated by a br tag', function (): void {
    $this->get('nesting')
        ->assertContainsElement('small.multi-line', ['text' => 'Foo Bar']);
});

it('assertContainsElement accepts integer attribute value', function (): void {
    $this->get('nesting')
        ->assertContainsElement('nav', ['data-id' => 42]);
});

it('assertContainsElement matches any element not just the first', function (): void {
    $this->get('nesting')
        ->assertContainsElement('li', ['data-id' => 1])
        ->assertContainsElement('li', ['data-id' => 2]);
});

it('can handle an empty view', function (): void {
    $this->get('empty')
        ->assertElementExists();
})->throws(
    AssertionFailedError::class,
    'The view is empty!'
);

it('can handle an empty body', function (): void {
    $this->get('empty-body')
        ->assertElementExists();
})->throws(
    AssertionFailedError::class,
    'No element found with selector: body'
);

it('can parse broken html', function (): void {
    $this->get('broken')
        ->assertElementExists();
});

it('can find the an element', function (): void {
    $this->get('nesting')
        ->assertElementExists();
});

it('can find the body', function (): void {
    $this->get('nesting')
        ->assertElementExists('body', static function (AssertElement $assert): void {
            $assert->is('body');
        });
});

it('can check for html5', function (): void {
    $this->get('nesting')
        ->assertHtml5();
});

it('can fail checking for html5', function (): void {
    $this->get('livewire')
        ->assertHtml5();
})->throws(
    AssertionFailedError::class,
    'Not a html5 doctype!'
);

it('can find the head', function (): void {
    $this->get('nesting')
        ->assertElementExists('head', static function (AssertElement $assert): void {
            $assert->is('head');
        });
});

it('can find a meta tag', function (): void {
    $this->get('nesting')
        ->assertElementExists('head', static function (AssertElement $assert): void {
            $assert->contains('meta', [
                'charset' => 'UTF-8',
            ]);
        });
});

it('can find a title', function (): void {
    $this->get('nesting')
        ->assertElementExists('head', static function (AssertElement $assert): void {
            $assert->find('title', static function (AssertElement $element): void {
                $element->has('text', 'Nesting');
            });
        });
});

it('can fail finding a class', function (): void {
    $this->get('nesting')
        ->assertElementExists('body', static function (AssertElement $assert): void {
            $assert->find('#nav', static function (AssertElement $element): void {
                $element->doesntHave('class');
            });
        });
});

it('can fail finding a href with exact match', function (): void {
    $this->get('nesting')
        ->assertElementExists('body', static function (AssertElement $assert): void {
            $assert->find('#nav a', static function (AssertElement $element): void {
                $element->has('href')
                    ->doesntHave('href', '/bar');
            });
        });
});

it('can fail when finding a id that isnt expected', function (): void {
    $this->get('nesting')
        ->assertElementExists('body', static function (AssertElement $assert): void {
            $assert->find('#nav', static function (AssertElement $element): void {
                $element->doesntHave('id');
            });
        });
})->throws(
    AssertionFailedError::class,
    'Found an attribute "id"'
);

it('can fail when finding a href with matching value that isnt expected', function (): void {
    $this->get('nesting')
        ->assertElementExists('body', static function (AssertElement $assert): void {
            $assert->find('#nav a', static function (AssertElement $element): void {
                $element->doesntHave('href', '/foo');
            });
        });
})->throws(
    AssertionFailedError::class,
    'Found an attribute "href" with value "/foo"'
);

it('can find an element by selector', function (): void {
    $this->get('nesting')
        ->assertElementExists('#nav');
});

it('can fail finding anything', function (): void {
    $this->get('nesting')
        ->assertElementExists('div > nav');
})->throws(
    AssertionFailedError::class,
    'No element found with selector: div > nav'
);

it('can check the element has the correct type', function (): void {
    $this->get('nesting')
        ->assertElementExists('#nav', static function (AssertElement $element): void {
            $element->is('nav');
        });
});

it('can fail matching element type', function (): void {
    $this->get('nesting')
        ->assertElementExists('#nav', static function (AssertElement $element): void {
            $element->is('div');
        });
})->throws(
    AssertionFailedError::class,
    'Element is not of type "div"'
);

it('can fail with wrong type of selector', function (): void {
    $this->get('form')
        ->assertElementExists(['div']);
})->throws(AssertionFailedError::class, 'Invalid selector!');

it('can find a nested element', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->containsDiv();
        });
});

it('can find a nested element with content', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('div', [
                'class' => 'foobar',
            ]);
        });
});

it('can match text content', function (): void {
    $this->get('nesting')
        ->assertElementExists('span.bar', static function (AssertElement $element): void {
            $element->has('text', 'Foo');
        });
});

it('can match text content with duplicate spaces and vertical whitespace', function (): void {
    $this->get('nesting')
        ->assertElementExists('p.foo.bar', static function (AssertElement $element): void {
            $element->has('text', 'Foo Bar');
        });
});

it('can match text content containing a string', function (): void {
    $this->get('nesting')
        ->assertElementExists('p.foo.bar', static function (AssertElement $element): void {
            $element->containsText('Bar');
        });
});

it('can match text content containing a string ignoring case', function (): void {
    $this->get('nesting')
        ->assertElementExists('p.foo.bar', static function (AssertElement $element): void {
            $element->containsText('bar', true);
        });
});

it('can match text content not containing a string', function (): void {
    $this->get('nesting')
        ->assertElementExists('p.foo.bar', static function (AssertElement $element): void {
            $element->doesntContainText('bar');
        });
});

it('can match a class no matter the order', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('span', [
                'class' => 'foo bar',
            ]);
            $element->find('span', static function (AssertElement $span): void {
                $span->has('class', 'foo bar');
            });
        });
});

it('can match a partial class', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('span', [
                'class' => 'foo bar',
            ]);
            $element->find('span', static function (AssertElement $span): void {
                $span->has('class', 'bar');
            });
        });
});

it('can find multiple identical items', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('div', [], 4);
        });
});

it('can find multiple identical items simplified', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('div', 4);
        });
});

it('can find multiple identical items with content', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('ul > li', [
                'x-data' => 'foobar',
            ], 2);
        });
});

it('can find multiple identical items with content ensuring no wrong matches', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('div', [
                'x-data' => 'foobar',
            ], 1);
        });
});

it('can fail finding a nested element with content', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('div', [
                'class' => 'foo',
            ]);
        });
})->throws(AssertionFailedError::class, 'Could not find a matching "div" with data:');

it('can run assertions against all elements that match the selection', function (): void {
    $this->get('form')
        ->assertOk()
        ->assertElementExists(static fn (AssertElement $view): BaseAssert => $view
            ->each('select', static fn (AssertElement $select): BaseAssert => $select->has('name'))
        );
});

it('can run assertions against indexed elements that match the selection', function (): void {
    $this->get('form')
        ->assertOk()
        ->assertElementExists(static function (AssertElement $view): void {
            $all = $view->getParser()->queryAll('select');
            $view->each('select', static fn (AssertElement $select, int $index): BaseAssert => $select->containsText($all->item($index)->nodeValue));
        });
});

it('fails when each() is used but no elements match the selector', function (): void {
    $this->get('form')
        ->assertOk()
        ->assertElementExists(static fn (AssertElement $view): BaseAssert => $view
            ->each('img', static fn (AssertElement $image): BaseAssert => $image->has('alt'))
        );
})->throws(AssertionFailedError::class);

it('can find a nested element with content functional', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->findDiv(static function (AssertElement $element): void {
                $element->is('div');
            });
        });
});

it('can find a nested element multiple levels', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->findDiv(static function (AssertElement $element): void {
                $element->is('div');
                $element->find('div', static function (AssertElement $element): void {
                    $element->is('div');
                    $element->findDiv(static function (AssertElement $element): void {
                        $element->is('div');
                    });
                });
            });
        });
});

it('can find a nested element multiple levels by query', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->findDiv(static function (AssertElement $element): void {
                $element->is('div');
                $element->find('.deep', static function (AssertElement $element): void {
                    $element->is('div');
                    $element->findSpan(static function (AssertElement $element): void {
                        $element->is('span');
                    });
                });
            });
        });
});

it('can find a nested element multiple levels by query and attributes', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->findDiv(static function (AssertElement $element): void {
                $element->is('div');
                $element->contains('.deep', [
                    'class' => 'deep',
                ]);
            });
        });
});

it('can find a nested element and ensure doesnt contain', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->findDiv(static function (AssertElement $element): void {
                $element->is('div');
                $element->doesntContain('nav');
            });
        });
});

it('can fail finding an contained element', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->findDiv(static function (AssertElement $element): void {
                $element->doesntContain('div');
            });
        });
})->throws(
    AssertionFailedError::class,
    'Found a matching element of type "div'
);

it('can fail finding an contained element with query', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->findDiv(static function (AssertElement $element): void {
                $element->doesntContain('div.foobar');
            });
        });
})->throws(
    AssertionFailedError::class,
    'Found a matching element of type "div'
);

it('can match on livewire attributes', function (): void {
    $this->get('livewire')
        ->assertOk()
        ->assertElementExists('[wire\:model="foo"]', static function (AssertElement $element): void {
            $element->is('input');
        });
});

it('can match has on livewire attributes', function (): void {
    $this->get('livewire')
        ->assertOk()
        ->assertElementExists('input', static function (AssertElement $element): void {
            $element->has('wire:model', 'foo');
        });
});

it('can match on livewire with contains', function (): void {
    $this->get('livewire')
        ->assertOk()
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('input[wire\:model="foo"]');
        });
});

it('can match on livewire contains as attribute', function (): void {
    $this->get('livewire')
        ->assertOk()
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('input', [
                'wire:model' => 'foo',
            ]);
        });
});

it('includes dom selectors when nesting and errors occur', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->findDiv(static function (AssertElement $element): void {
                $element->is('div');
                $element->find('div', static function (AssertElement $element): void {
                    $element->is('div');
                    $element->findDiv(static function (AssertElement $element): void {
                        $element->contains('not-existing');
                    });
                });
            });
        });
})->throws(AssertionFailedError::class, 'Could not find any matching element of type "not-existing" within: div > div > div');

it('includes dom selectors when only one deep', function (): void {
    $this->get('nesting')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->find('.foobar', static function (AssertElement $element): void {
                $element->is('not-real');
            });
        });
})->throws(AssertionFailedError::class, 'Element is not of type "not-real" within: .foobar');

it('can run the example from the readme', function (): void {
    $this->get(route('about'))
        ->assertOk()
        ->assertElementExists('nav > ul', static function (AssertElement $ul): void {
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

it('multiple views can be tested in the same test', function (): void {
    $this->get('nesting')
        ->assertDoesntExist('span.fake')
        ->assertDoesntExist('nav.fake')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('nav');
        });

    $this->get('livewire')
        ->assertElementExists('input')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('input[wire\:model="foo"]');
        });

    $this->get('nesting')
        ->assertDoesntExist('span.fake')
        ->assertDoesntExist('nav.fake')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('nav');
        });

    $this->get('livewire')
        ->assertElementExists('input')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('input[wire\:model="foo"]');
        });

    $this->get('nesting')
        ->assertContainsElement('span.foo', ['text' => 'Foo']);

    expect(fn () => $this->get('livewire')->assertContainsElement('span.foo', ['text' => 'Foo']))
        ->toThrow(AssertionFailedError::class, 'No element found with selector: span.foo');

});
