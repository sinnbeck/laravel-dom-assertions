<?php

use PHPUnit\Framework\AssertionFailedError;
use Sinnbeck\DomAssertions\Asserts\FormAssert;
use Sinnbeck\DomAssertions\Asserts\OptionAssert;
use Sinnbeck\DomAssertions\Asserts\SelectAssert;

it('can find a form by default', function () {
    $this->get('form')
        ->assertForm(function (FormAssert $form) {
            $form->hasAction('store-comment');
        });
});

it('can find a form by index', function () {
    $this->get('form')
        ->assertForm(function (FormAssert $form) {
            $form->hasAction('form');
        }, 1);
});

it('can find a form by css selector', function () {
    $this->get('form')
        ->assertForm(function (FormAssert $form) {
            $form->hasAction('form');
        }, 'form:nth-child(2)');
});

it('can fail to find a form', function () {
    $this->expectException(AssertionFailedError::class);
    $this->expectExceptionMessage('Element is not of type form!');
    $this->get('form')
        ->assertForm(function (FormAssert $form) {
            $form->hasAction('form');
        }, 'div');
});

it('can fail with wrong type of selector', function () {
    $this->expectException(AssertionFailedError::class);
    $this->expectExceptionMessage('Invalid selector!');
    $this->get('form')
        ->assertForm(function (FormAssert $form) {
            $form->hasAction('form');
        }, ['form']);
});

it('can fail to find anything', function () {
    $this->expectException(AssertionFailedError::class);
    $this->expectExceptionMessage('No form was found with selector: 10');
    $this->get('form')
        ->assertForm(function (FormAssert $form) {
            $form->hasAction('form');
        }, 10);
});

it('can find elements', function () {
    $this->get('form')
        ->assertForm(function (FormAssert $form) {
            $form->hasAction('/form')
                ->hasMethod('post')
                ->hasCSRF()
                ->hasSpoofMethod('PUT');
        }, '#form2')
        ->assertOk();
});

it('can find enc type', function () {
    $this->get('form')
        ->assertForm(function (FormAssert $form) {
            $form->hasEnctype('multipart/form-data');
        }, '#form1')
        ->assertOk();
});


it('can find inputs', function () {
    $this->get('form')
        ->assertForm(function (FormAssert $form) {
            $form->hasAction('/form')
                ->hasMethod('post')
                ->hasCSRF()
                ->hasSpoofMethod('PUT')
                ->containsInput([
                    'name' => 'first_name',
                    'type' => 'text',
                    'value' => 'Foo',
                ])
                ->containsInput([
                    'name' => 'tags[]',
                    'type' => 'text',
                    'value' => 'Happy',
                ])
                ->containsInput([
                    'name' => 'tags[]',
                    'type' => 'text',
                    'value' => 'Buys cheese',
                ]);
        }, '#form2')
        ->assertOk();
});

it('can detect a missing input', function () {
    $this->get('form')
        ->assertForm(function (FormAssert $form) {
            $form->hasAction('/form')
                ->hasMethod('post')
                ->doesntContainInput([
                    'name' => 'last_name',
                ]);
        }, '#form2')->assertOk();
});

it('can ignore an input outside the form ', function () {
    $this->get('form')
        ->assertForm(function (FormAssert $form) {
            $form->hasAction('/form')
                ->hasMethod('post')
                ->doesntContainInput([
                    'name' => 'outside',
                ]);
        }, '#form2')->assertOk();
});

it('can test a textarea', function () {
    $this->get('form')
        ->assertForm(function (FormAssert $form) {
            $form->containsTextarea([
                'name' => 'comment',
                'value' => 'foo',
            ]);
        })->assertOk();
});

it('can parse a select with options', function () {
    $this->get('form')
        ->assertForm(function (FormAssert $form) {
            $form->containsSelect(function (SelectAssert $selectAssert) {
                $selectAssert->has('name', 'country')
                    ->containsOption([
                        'x-data' => 'none',
                        'value' => 'none',
                        'text' => 'None',
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
            }, 'select:nth-of-type(2)');
        }, '#form2')->assertOk();
});

it('can parse a select with options functional', function () {
    $this->get('form')
        ->assertForm(function (FormAssert $form) {
            $form->containsSelect(function (SelectAssert $selectAssert) {
                $selectAssert->has('name', 'country')
                    ->containsOption(function (OptionAssert $optionAssert) {
                        $optionAssert->hasValue('none');
                        $optionAssert->hasText('None');
                    },)
                    ->containsOptions(
                        function (OptionAssert $optionAssert) {
                            $optionAssert->hasValue('dk');
                            $optionAssert->hasText('Denmark');
                        },
                        function (OptionAssert $optionAssert) {
                            $optionAssert->hasValue('us')
                                ->hasText('USA');
                        },
                    );
            }, 'select:nth-of-type(2)');
        }, '#form2')->assertOk();
});

it('can find a button', function () {
    $this->get('form')
        ->assertForm(function (FormAssert $form) {
            $form->containsButton([
                'type' => 'submit',
            ]);
        }, '#form2')->assertOk();
});

it('can check arbitrary attributes', function () {
    $this->get('form')
        ->assertForm(function (FormAssert $form) {
            $form->hasXData('foo');
        })->assertOk();
});

it('can check arbitrary children', function () {
    $this->get('form')
        ->assertForm(function (FormAssert $form) {
            $form->containsLabel([
                'for' => 'bar',
            ]);
        })->assertOk();
});
