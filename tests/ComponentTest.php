<?php

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use Sinnbeck\DomAssertions\Asserts\AssertForm;
use Sinnbeck\DomAssertions\Asserts\AssertSelect;
use Tests\Views\Components\BrokenComponent;
use Tests\Views\Components\EmptyBodyComponent;
use Tests\Views\Components\EmptyComponent;
use Tests\Views\Components\FormComponent;
use Tests\Views\Components\Html5Component;
use Tests\Views\Components\LivewireAttributeComponent;
use Tests\Views\Components\NestedComponent;

beforeEach(function (): void {
    if (! version_compare(app()->version(), '11.41.0', '>=')) {
        TestCase::markTestSkipped('Testing Blade components is unavailable in this version of Laravel');
    }
});

it('assertElement alias works for assertElementExists', function (): void {
    $this->component(NestedComponent::class)
        ->assertElement('body', static function (AssertElement $assert): void {
            $assert->is('body');
        });
});

it('assertDoesntExist works as expected', function (): void {
    $this->component(NestedComponent::class)
        ->assertDoesntExist('span.fake')
        ->assertDoesntExist('nav.fake');
});

it('assertContainsElement works as expected', function (): void {
    $this->component(NestedComponent::class)
        ->assertContainsElement('span.foo', ['text' => 'Foo', 'class' => 'bar foo'])
        ->assertContainsElement('nav');
});

it('assertContainsElement throws if selector not found', function (): void {
    $this->component(NestedComponent::class)
        ->assertContainsElement('span.non-existing', ['text' => 'Foo']);
})->throws(AssertionFailedError::class, 'No element found with selector: span.non-existing');

it('assertContainsElement throws if contains text does not exist', function (): void {
    $this->component(NestedComponent::class)
        ->assertContainsElement('span.foo', ['text' => 'non-existing']);
})->throws(AssertionFailedError::class, 'Failed asserting that element [span.foo] text contains "non-existing". Actual: "Foo"');

it('assertContainsElement throws if contains attribute does not exist', function (): void {
    $this->view('nesting')
        ->assertContainsElement('span.foo', ['non-existing-attribute' => 'non-existing']);
})->throws(AssertionFailedError::class, 'Attribute [non-existing-attribute] not found in element [span.foo]');

it('assertContainsElement matches text across child elements', function (): void {
    $this->get('nesting')
        ->assertContainsElement('p.foo', ['text' => 'Foo Bar']);
});

it('assertContainsElement matches text separated by a br tag', function (): void {
    $this->get('nesting')
        ->assertContainsElement('small.multi-line', ['text' => 'Foo Bar']);
});

it('assertFormExists works as expects', function (): void {
    $this->component(FormComponent::class)
        ->assertFormExists('#form1', static function (AssertForm $form): void {
            $form->hasAction('store-comment');
        });
});

it('assertSelectExists works as expects', function (): void {
    $this->component(FormComponent::class)
        ->assertSelectExists('[name="things"]', static function (AssertSelect $select): void {
            $select->containsOptgroups(
                ['label' => 'Animals'],
                ['label' => 'Vegetables'],
                ['label' => 'Minerals'],
            );
            $select->containsOptions(
                ['value' => 'dog'],
                ['value' => 'carrot'],
                ['value' => 'calcium'],
            );
        });
});

it('can handle an empty component', function (): void {
    $this->component(EmptyComponent::class)
        ->assertElementExists();
})->throws(
    AssertionFailedError::class,
    'The component is empty!'
);

it('can handle an empty body', function (): void {
    $this->component(EmptyBodyComponent::class)
        ->assertElementExists();
})->throws(
    AssertionFailedError::class,
    'No element found with selector: body'
);

it('can parse broken html', function (): void {
    $this->component(BrokenComponent::class)
        ->assertElementExists();
});

it('can find the element', function (): void {
    $this->component(NestedComponent::class)
        ->assertElementExists();
});

it('can find the body', function (): void {
    $this->component(NestedComponent::class)
        ->assertElementExists('body', static function (AssertElement $assert): void {
            $assert->is('body');
        });
});

it('can check for html5', function (): void {
    $this->component(Html5Component::class)
        ->assertHtml5();
});

it('can fail checking for html5', function (): void {
    $this->component(NestedComponent::class)
        ->assertHtml5();
})->throws(
    AssertionFailedError::class,
    'Not a html5 doctype!'
);

it('can fail finding a class', function (): void {
    $this->component(Html5Component::class)
        ->assertElementExists('body', static function (AssertElement $assert): void {
            $assert->find('#nav', static function (AssertElement $element): void {
                $element->doesntHave('class');
            });
        });
});

it('can fail finding a href with exact match', function (): void {
    $this->component(Html5Component::class)
        ->assertElementExists('body', static function (AssertElement $assert): void {
            $assert->find('#nav a', static function (AssertElement $element): void {
                $element->has('href')
                    ->doesntHave('href', '/bar');
            });
        });
});

it('can fail when finding a id that isnt expected', function (): void {
    $this->component(Html5Component::class)
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
    $this->component(Html5Component::class)
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
    $this->component(Html5Component::class)
        ->assertElementExists('#nav');
});

it('can fail finding anything', function (): void {
    $this->component(Html5Component::class)
        ->assertElementExists('div > nav');
})->throws(
    AssertionFailedError::class,
    'No element found with selector: div > nav'
);

it('can check the element has the correct type', function (): void {
    $this->component(Html5Component::class)
        ->assertElementExists('#nav', static function (AssertElement $element): void {
            $element->is('nav');
        });
});

it('can fail matching element type', function (): void {
    $this->component(Html5Component::class)
        ->assertElementExists('#nav', static function (AssertElement $element): void {
            $element->is('div');
        });
})->throws(
    AssertionFailedError::class,
    'Element is not of type "div"'
);

it('can fail with wrong type of selector', function (): void {
    $this->view('form')
        ->assertElementExists(['div']);
})->throws(
    AssertionFailedError::class,
    'Invalid selector!'
);

it('can find a nested element', function (): void {
    $this->component(Html5Component::class)
        ->assertElementExists(static function (AssertElement $element): void {
            $element->containsDiv();
        });
});

it('can find a nested element with content', function (): void {
    $this->component(Html5Component::class)
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('div', [
                'class' => 'foobar',
            ]);
        });
});

it('can match text content', function (): void {
    $this->component(Html5Component::class)
        ->assertElementExists('span.bar', static function (AssertElement $element): void {
            $element->has('text', 'Foo');
        });
});

it('can match text content with duplicate spaces and vertical whitespace', function (): void {
    $this->component(Html5Component::class)
        ->assertElementExists('p.foo.bar', static function (AssertElement $element): void {
            $element->has('text', 'Foo Bar');
        });
});

it('can match text content containing a string', function (): void {
    $this->component(Html5Component::class)
        ->assertElementExists('p.foo.bar', static function (AssertElement $element): void {
            $element->containsText('Bar');
        });
});

it('can match text content containing a string ignoring case', function (): void {
    $this->component(Html5Component::class)
        ->assertElementExists('p.foo.bar', static function (AssertElement $element): void {
            $element->containsText('bar', true);
        });
});

it('can match text content not containing a string', function (): void {
    $this->component(Html5Component::class)
        ->assertElementExists('p.foo.bar', static function (AssertElement $element): void {
            $element->doesntContainText('bar');
        });
});

it('can match a class no matter the order', function (): void {
    $this->component(Html5Component::class)
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
    $this->component(Html5Component::class)
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
    $this->component(Html5Component::class)
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('div', [], 4);
        });
});

it('can find multiple identical items simplified', function (): void {
    $this->component(Html5Component::class)
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('div', 4);
        });
});

it('can find multiple identical items with content', function (): void {
    $this->component(Html5Component::class)
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('ul > li', [
                'x-data' => 'foobar',
            ], 2);
        });
});

it('can find multiple identical items with content ensuring no wrong matches', function (): void {
    $this->component(Html5Component::class)
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('div', [
                'x-data' => 'foobar',
            ], 1);
        });
});

it('can fail finding a nested element with content', function (): void {
    $this->component(Html5Component::class)
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('div', [
                'class' => 'foo',
            ]);
        });
})->throws(
    AssertionFailedError::class,
    'Could not find a matching "div" with data:'
);

it('can find a nested element with content functional', function (): void {
    $this->component(Html5Component::class)
        ->assertElementExists(static function (AssertElement $element): void {
            $element->findDiv(static function (AssertElement $element): void {
                $element->is('div');
            });
        });
});

it('can find a nested element multiple levels', function (): void {
    $this->component(Html5Component::class)
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
    $this->component(Html5Component::class)
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
    $this->component(Html5Component::class)
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
    $this->component(Html5Component::class)
        ->assertElementExists(static function (AssertElement $element): void {
            $element->findDiv(static function (AssertElement $element): void {
                $element->is('div');
                $element->doesntContain('nav');
            });
        });
});

it('can fail finding an contained element', function (): void {
    $this->component(Html5Component::class)
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
    $this->component(Html5Component::class)
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
    $this->component(LivewireAttributeComponent::class)
        ->assertElementExists('[wire\:model="foo"]', static function (AssertElement $element): void {
            $element->is('input');
        });
});

it('can match has on livewire attributes', function (): void {
    $this->component(LivewireAttributeComponent::class)
        ->assertElementExists('input', static function (AssertElement $element): void {
            $element->has('wire:model', 'foo');
        });
});

it('can match on livewire with contains', function (): void {
    $this->component(LivewireAttributeComponent::class)
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('input[wire\:model="foo"]');
        });
});

it('can match on livewire contains as attribute', function (): void {
    $this->component(LivewireAttributeComponent::class)
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('input', [
                'wire:model' => 'foo',
            ]);
        });
});

it('multiple views can be tested in the same test', function (): void {
    $this->component(NestedComponent::class)
        ->assertDoesntExist('span.fake')
        ->assertDoesntExist('nav.fake')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('nav');
        });

    $this->component(LivewireAttributeComponent::class)
        ->assertElementExists('input')
        ->assertElementExists(static function (AssertElement $element): void {
            $element->contains('input[wire\:model="foo"]');
        });

    $this->component(NestedComponent::class)
        ->assertContainsElement('span.foo', ['text' => 'Foo']);

    expect(fn () => $this->component(LivewireAttributeComponent::class)->assertContainsElement('span.foo', ['text' => 'Foo']))
        ->toThrow(AssertionFailedError::class, 'No element found with selector: span.foo');
});
