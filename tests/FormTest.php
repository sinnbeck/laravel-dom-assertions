<?php

use PHPUnit\Framework\AssertionFailedError;
use Sinnbeck\DomAssertions\Asserts\FormAssert;
use Sinnbeck\DomAssertions\Asserts\OptionAssert;
use Sinnbeck\DomAssertions\Asserts\SelectAssert;

it('can find a form by default', function () {
    $this->get('form')
        ->assertForm();
});

it('can find a form by css selector', function () {
    $this->get('form')
        ->assertForm('form:nth-child(2)', function (FormAssert $form) {
            $form->hasAction('form');
        });
});

it('can fail to find a form', function () {
    $this->get('form')
        ->assertForm('div', function (FormAssert $form) {
            $form->hasAction('form');
        });
})->throws(AssertionFailedError::class, 'Element is not of type form!');

it('can fail with wrong type of selector', function () {
    $this->get('form')
        ->assertForm(['form'], function (FormAssert $form) {
            $form->hasAction('form');
        });
})->throws(AssertionFailedError::class, 'Invalid selector!');

it('can fail to find anything', function () {
    $this->get('form')
        ->assertForm('foobar', function (FormAssert $form) {
            $form->hasAction('form');
        });
})->throws(AssertionFailedError::class, 'No form was found with selector "foobar"');

it('can find elements', function () {
    $this->get('form')
        ->assertForm('#form2', function (FormAssert $form) {
            $form->hasAction('/form')
                ->hasMethod('post')
                ->hasCSRF()
                ->hasSpoofMethod('PUT');
        })
        ->assertOk();
});

it('can pass no spoff methods', function () {
    $this->get('form')
        ->assertForm('#form2', function (FormAssert $form) {
            $form->hasAction('/form')
                ->hasMethod('PUT');
        })
        ->assertOk();
});

it('can find enc type', function () {
    $this->get('form')
        ->assertForm('#form1', function (FormAssert $form) {
            $form->hasEnctype('multipart/form-data');
        })
        ->assertOk();
});

it('can find inputs', function () {
    $this->get('form')
        ->assertForm('#form2', function (FormAssert $form) {
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
        })
        ->assertOk();
});

it('can detect a missing input', function () {
    $this->get('form')
        ->assertForm('#form2', function (FormAssert $form) {
            $form->hasAction('/form')
                ->hasMethod('post')
                ->doesntContainInput([
                    'name' => 'last_name',
                ]);
        })->assertOk();
});

it('can ignore an input outside the form ', function () {
    $this->get('form')
        ->assertForm('#form2', function (FormAssert $form) {
            $form->hasAction('/form')
                ->hasMethod('post')
                ->doesntContainInput([
                    'name' => 'outside',
                ]);
        })->assertOk();
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
        ->assertForm('#form2', function (FormAssert $form) {
            $form->containsSelect('select:nth-of-type(2)', function (SelectAssert $selectAssert) {
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
            });
        })->assertOk();
});

it('can parse a select with options functional', function () {
    $this->get('form')
        ->assertForm('#form2', function (FormAssert $form) {
            $form->containsSelect('select:nth-of-type(2)', function (SelectAssert $selectAssert) {
                $selectAssert->has('name', 'country')
                    ->containsOption(function (OptionAssert $optionAssert) {
                        $optionAssert->hasValue('none');
                        $optionAssert->hasText('None');
                    }, )
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
            });
        })->assertOk();
});

it('can assert that select has value', function () {
    $this->get('form')
        ->assertForm('#form2', function (FormAssert $form) {
            $form->containsSelect('select:nth-of-type(2)', function (SelectAssert $selectAssert) {
                $selectAssert->hasValue('none');
            });
        })->assertOk();
});

it('can assert that option is selected', function () {
    $this->get('form')
        ->assertForm('#form2', function (FormAssert $form) {
            $form->containsSelect('select:nth-of-type(2)', function (SelectAssert $selectAssert) {
                $selectAssert->containsOption(function (OptionAssert $optionAssert) {
                    $optionAssert->hasValue('none');
                    $optionAssert->hasText('None');
                    $optionAssert->isSelected();
                });
            });
        })->assertOk();
});

it('can find a button', function () {
    $this->get('form')
        ->assertForm('#form2', function (FormAssert $form) {
            $form->containsButton([
                'type' => 'submit',
            ]);
        })->assertOk();
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
                'for' => 'comment',
            ]);
        })->assertOk();
});
