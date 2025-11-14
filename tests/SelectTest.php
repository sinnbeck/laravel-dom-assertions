<?php

use PHPUnit\Framework\AssertionFailedError;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use Sinnbeck\DomAssertions\Asserts\AssertForm;
use Sinnbeck\DomAssertions\Asserts\AssertSelect;

it('assertSelect alias works for assertSelectExists', function () {
    $this->get('form')
        ->assertSelect(function (AssertSelect $select) {
            $select->containsOption(['value' => 'fi']);
        });
});

it('can find a select by default', function () {
    $this->get('form')
        ->assertSelectExists();
});

it('can find a select by css selector', function () {
    $this->get('form')
        ->assertSelectExists('select:nth-of-type(2)', function (AssertSelect $select) {
            $select->containsOption(['value' => 'dk']);
        });
});

it('can fail to find a select', function () {
    $this->get('form')
        ->assertSelectExists('div', function (AssertSelect $select) {
            $select->contains('option');
        });
})->throws(AssertionFailedError::class, 'Element is not of type select!');

it('can fail with wrong type of selector', function () {
    $this->get('form')
        ->assertSelectExists(['select'], function (AssertSelect $select) {
            $select->contains('option');
        });
})->throws(AssertionFailedError::class, 'Invalid selector!');

it('can fail to find anything', function () {
    $this->get('form')
        ->assertSelectExists('foobar', function (AssertSelect $select) {
            $select->contains('option');
        });
})->throws(AssertionFailedError::class, 'No select was found with selector "foobar"');

it('can ignore an option outside the select ', function () {
    $this->get('form')
        ->assertSelectExists(function (AssertSelect $select) {
            $select->doesntContain('option', ['value' => 'dog']);
        })->assertOk();
});

it('can parse a select with options', function () {
    $this->get('form')
        ->assertSelectExists('select:nth-of-type(2)', function (AssertSelect $select) {
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

it('can parse a select with optgroups', function () {
    $this->get('form')
        ->assertSelectExists('select:nth-of-type(3)', function (AssertSelect $select) {
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

it('can parse a select with options functional', function () {
    $this->get('form')
        ->assertSelectExists('select:nth-of-type(2)', function (AssertSelect $select) {
            $select->has('name', 'country')
                ->findOption(function (AssertElement $optionAssert) {
                    $optionAssert->hasValue('none');
                    $optionAssert->hasText('None');
                });
        })->assertOk();
});

it('can assert that select has value', function () {
    $this->get('form')
        ->assertSelectExists('select:nth-of-type(2)', function (AssertSelect $select) {
            $select->hasValue('none');
        })->assertOk();
});

it('can assert that option is selected', function () {
    $this->get('form')
        ->assertFormExists('#form2', function (AssertForm $form) {
            $form->findSelect('select:nth-of-type(2)', function (AssertSelect $selectAssert) {
                $selectAssert->findOption(function (AssertElement $option) {
                    $option->hasValue('none');
                    $option->hasText('None');
                    $option->hasSelected();
                });
            });
        })->assertOk();
});

it('can assert that select has multiple values', function () {
    $this->get('form')
        ->assertFormExists('#form2', function (AssertForm $form) {
            $form->findSelect('select', function (AssertSelect $select) {
                $select->hasValues(['da', 'en']);
            });
        })->assertOk();
});

it('can check arbitrary attributes', function () {
    $this->get('form')
        ->assertSelectExists(function (AssertSelect $select) {
            $select->hasXData('bar');
        })->assertOk();
});
