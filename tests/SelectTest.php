<?php

use PHPUnit\Framework\AssertionFailedError;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use Sinnbeck\DomAssertions\Asserts\AssertForm;
use Sinnbeck\DomAssertions\Asserts\AssertSelect;

it('assertSelect alias works for assertSelectExists', function (): void {
    $this->get('form')
        ->assertSelect(static function (AssertSelect $select): void {
            $select->containsOption(['value' => 'fi']);
        });
});

it('can find a select by default', function (): void {
    $this->get('form')
        ->assertSelectExists();
});

it('can find a select by css selector', function (): void {
    $this->get('form')
        ->assertSelectExists('select:nth-of-type(2)', static function (AssertSelect $select): void {
            $select->containsOption(['value' => 'dk']);
        });
});

it('can fail to find a select', function (): void {
    $this->get('form')
        ->assertSelectExists('div', static function (AssertSelect $select): void {
            $select->contains('option');
        });
})->throws(AssertionFailedError::class, 'Element is not of type select!');

it('can fail with wrong type of selector', function (): void {
    $this->get('form')
        ->assertSelectExists(['select'], static function (AssertSelect $select): void {
            $select->contains('option');
        });
})->throws(AssertionFailedError::class, 'Invalid selector!');

it('can fail to find anything', function (): void {
    $this->get('form')
        ->assertSelectExists('foobar', static function (AssertSelect $select): void {
            $select->contains('option');
        });
})->throws(AssertionFailedError::class, 'No select was found with selector "foobar"');

it('can ignore an option outside the select ', function (): void {
    $this->get('form')
        ->assertSelectExists(static function (AssertSelect $select): void {
            $select->doesntContain('option', ['value' => 'dog']);
        })->assertOk();
});

it('can parse a select with options', function (): void {
    $this->get('form')
        ->assertSelectExists('select:nth-of-type(2)', static function (AssertSelect $select): void {
            $select->has('name', 'country')
                ->containsOption([
                    'x-data' => 'none',
                    'value' => 'none',
                    'text' => 'None',
                    'selected' => 'selected',
                ])
                ->containsOptions(
                    [
                        'value' => 'dk',
                        'text' => 'Denmark',
                    ],
                    [
                        'value' => 'us',
                        'text' => 'USA',
                    ],

                );
        })->assertOk();
});

it('can parse a select with optgroups', function (): void {
    $this->get('form')
        ->assertSelectExists('select:nth-of-type(3)', static function (AssertSelect $select): void {
            $select->has('name', 'things')
                ->containsOptgroup([
                    'label' => 'Animals',
                ])
                ->containsOptgroups(
                    [
                        'label' => 'Vegetables',
                        'x-data' => 'none',
                    ],
                    [
                        'label' => 'Minerals',
                    ]
                )
                ->containsOptions(
                    [
                        'value' => 'dog',
                        'text' => 'Dog',
                    ],
                    [
                        'value' => 'cat',
                        'text' => 'Cat',
                    ],
                    [
                        'value' => 'carrot',
                        'text' => 'Carrot',
                    ],
                    [
                        'value' => 'onion',
                        'text' => 'Onion',
                    ],
                    [
                        'value' => 'calcium',
                        'text' => 'Calcium',
                    ],
                    [
                        'value' => 'zinc',
                        'text' => 'Zinc',
                    ],
                );
        })->assertOk();
});

it('can parse a select with options functional', function (): void {
    $this->get('form')
        ->assertSelectExists('select:nth-of-type(2)', static function (AssertSelect $select): void {
            $select->has('name', 'country')
                ->findOption(static function (AssertElement $optionAssert): void {
                    $optionAssert->hasValue('none');
                    $optionAssert->hasText('None');
                });
        })->assertOk();
});

it('can assert that select has value', function (): void {
    $this->get('form')
        ->assertSelectExists('select:nth-of-type(2)', static function (AssertSelect $select): void {
            $select->hasValue('none');
        })->assertOk();
});

it('can assert that option is selected', function (): void {
    $this->get('form')
        ->assertFormExists('#form2', static function (AssertForm $form): void {
            $form->findSelect('select:nth-of-type(2)', static function (AssertSelect $selectAssert): void {
                $selectAssert->findOption(static function (AssertElement $option): void {
                    $option->hasValue('none');
                    $option->hasText('None');
                    $option->hasSelected();
                });
            });
        })->assertOk();
});

it('can assert that select has multiple values', function (): void {
    $this->get('form')
        ->assertFormExists('#form2', static function (AssertForm $form): void {
            $form->findSelect('select', static function (AssertSelect $select): void {
                $select->hasValues(['da', 'en']);
            });
        })->assertOk();
});

it('can check arbitrary attributes', function (): void {
    $this->get('form')
        ->assertSelectExists(static function (AssertSelect $select): void {
            $select->hasXData('bar');
        })->assertOk();
});
