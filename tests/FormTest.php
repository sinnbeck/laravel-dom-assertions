<?php

use PHPUnit\Framework\AssertionFailedError;
use Sinnbeck\DomAssertions\Asserts\AssertDatalist;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use Sinnbeck\DomAssertions\Asserts\AssertForm;

it('assertForm alias works for assertFormExists', function () {
    $this->get('form')
        ->assertForm('form:nth-child(2)', function (AssertForm $form) {
            $form->hasAction('form');
        });
});

it('can find a form by default', function () {
    $this->get('form')
        ->assertFormExists();
});

it('can find a form by css selector', function () {
    $this->get('form')
        ->assertFormExists('form:nth-child(2)', function (AssertForm $form) {
            $form->hasAction('form');
        });
});

it('can fail to find a form', function () {
    $this->get('form')
        ->assertFormExists('div', function (AssertForm $form) {
            $form->hasAction('form');
        });
})->throws(AssertionFailedError::class, 'Element is not of type form!');

it('can fail with wrong type of selector', function () {
    $this->get('form')
        ->assertFormExists(['form'], function (AssertForm $form) {
            $form->hasAction('form');
        });
})->throws(AssertionFailedError::class, 'Invalid selector!');

it('can fail to find anything', function () {
    $this->get('form')
        ->assertFormExists('foobar', function (AssertForm $form) {
            $form->hasAction('form');
        });
})->throws(AssertionFailedError::class, 'No form was found with selector "foobar"');

it('can find elements', function () {
    $this->get('form')
        ->assertFormExists('#form2', function (AssertForm $form) {
            $form->hasAction('/form')
                ->hasMethod('post')
                ->hasCSRF()
                ->hasSpoofMethod('PUT');
        })
        ->assertOk();
});

it('can assert method with wrong casing', function () {
    $this->get('form')
        ->assertFormExists('#form2', function (AssertForm $form) {
            $form->hasMethod('PoSt');
        })
        ->assertOk();
});

it('can pass no spoff methods', function () {
    $this->get('form')
        ->assertFormExists('#form2', function (AssertForm $form) {
            $form->hasAction('/form')
                ->hasMethod('PUT');
        })
        ->assertOk();
});

it('can pass no spoff methods with wrong casing', function () {
    $this->get('form')
        ->assertFormExists('#form2', function (AssertForm $form) {
            $form->hasAction('/form')
                ->hasMethod('puT')
                ->hasSpoofMethod('Put');
        })
        ->assertOk();
});

it('can find enc type', function () {
    $this->get('form')
        ->assertFormExists('#form1', function (AssertForm $form) {
            $form->hasEnctype('multipart/form-data');
        })
        ->assertOk();
});

it('can find inputs', function () {
    $this->get('form')
        ->assertFormExists('#form2', function (AssertForm $form) {
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
        ->assertFormExists('#form2', function (AssertForm $form) {
            $form->hasAction('/form')
                ->hasMethod('post')
                ->doesntContainInput([
                    'name' => 'last_name',
                ]);
        })->assertOk();
});

it('can ignore an input outside the form ', function () {
    $this->get('form')
        ->assertFormExists('#form2', function (AssertForm $form) {
            $form->hasAction('/form')
                ->hasMethod('post')
                ->doesntContainInput([
                    'name' => 'outside',
                ]);
        })->assertOk();
});

it('can test a textarea', function () {
    $this->get('form')
        ->assertFormExists(function (AssertForm $form) {
            $form->containsTextarea([
                'name' => 'comment',
                'value' => 'foo',
            ]);
        })->assertOk();
});

it('can test a textarea is required', function () {
    $this->get('form')
        ->assertFormExists(function (AssertForm $form) {
            $form->findTextarea(function (AssertElement $textArea) {
                $textArea->hasRequired();
            });
        })->assertOk();
});

it('can test a textarea has required true', function () {
    $this->get('form')
        ->assertFormExists(function (AssertForm $form) {
            $form->contains('textarea', [
                'required' => true,
            ]);
        })->assertOk();
});

it('can find no inputs with required', function () {
    $this->get('form')
        ->assertFormExists(function (AssertForm $form) {
            $form->doesntContain('input', [
                'required' => true,
            ]);
        })->assertOk();
});

it('can find a button', function () {
    $this->get('form')
        ->assertFormExists('#form2', function (AssertForm $form) {
            $form->containsButton([
                'type' => 'submit',
            ]);
        })->assertOk();
});

it('can check arbitrary attributes', function () {
    $this->get('form')
        ->assertFormExists(function (AssertForm $form) {
            $form->hasXData('foo');
        })->assertOk();
});

it('can check arbitrary children', function () {
    $this->get('form')
        ->assertFormExists(function (AssertForm $form) {
            $form->containsLabel([
                'for' => 'comment',
            ]);
        })->assertOk();
});

it('can parse a datalist with options', function () {
    $this->get('form')
        ->assertFormExists('#form2', function (AssertForm $form) {
            $form->findDatalist('#skills', function (AssertDataList $datalist) {
                $datalist->containsOption([
                    'value' => 'PHP',
                ])
                    ->containsOptions(
                        [
                            'value' => 'PHP',
                        ],
                        [
                            'value' => 'JavaScript',
                        ],
                    );
            });
        })->assertOk();
});

it('requires that the selector for datalist is an id', function () {
    $this->get('form')
        ->assertFormExists('#form2', function (AssertForm $form) {
            $form->findDatalist('.my-datalist', function (AssertDataList $datalist) {
                $datalist->containsOption([
                    'value' => 'My first value',
                ]);
            });
        });
})->throws(AssertionFailedError::class,
    'Selectors for datalists must be an id, given: .my-datalist');
