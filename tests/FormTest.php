<?php

use PHPUnit\Framework\AssertionFailedError;
use Sinnbeck\DomAssertions\Asserts\AssertDatalist;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use Sinnbeck\DomAssertions\Asserts\AssertForm;

it('assertForm alias works for assertFormExists', function (): void {
    $this->get('form')
        ->assertForm('form:nth-child(2)', static function (AssertForm $form): void {
            $form->hasAction('form');
        });
});

it('can find a form by default', function (): void {
    $this->get('form')
        ->assertFormExists();
});

it('can find a form by css selector', function (): void {
    $this->get('form')
        ->assertFormExists('form:nth-child(2)', static function (AssertForm $form): void {
            $form->hasAction('form');
        });
});

it('can fail to find a form', function (): void {
    $this->get('form')
        ->assertFormExists('div', static function (AssertForm $form): void {
            $form->hasAction('form');
        });
})->throws(AssertionFailedError::class, 'Element is not of type form!');

it('can fail with wrong type of selector', function (): void {
    $this->get('form')
        ->assertFormExists(['form'], static function (AssertForm $form): void {
            $form->hasAction('form');
        });
})->throws(AssertionFailedError::class, 'Invalid selector!');

it('can fail to find anything', function (): void {
    $this->get('form')
        ->assertFormExists('foobar', static function (AssertForm $form): void {
            $form->hasAction('form');
        });
})->throws(AssertionFailedError::class, 'No form was found with selector "foobar"');

it('can find elements', function (): void {
    $this->get('form')
        ->assertFormExists('#form2', static function (AssertForm $form): void {
            $form->hasAction('/form')
                ->hasMethod('post')
                ->hasCSRF()
                ->hasSpoofMethod('PUT');
        })
        ->assertOk();
});

it('can assert method with wrong casing', function (): void {
    $this->get('form')
        ->assertFormExists('#form2', static function (AssertForm $form): void {
            $form->hasMethod('PoSt');
        })
        ->assertOk();
});

it('can pass no spoff methods', function (): void {
    $this->get('form')
        ->assertFormExists('#form2', static function (AssertForm $form): void {
            $form->hasAction('/form')
                ->hasMethod('PUT');
        })
        ->assertOk();
});

it('can pass no spoff methods with wrong casing', function (): void {
    $this->get('form')
        ->assertFormExists('#form2', static function (AssertForm $form): void {
            $form->hasAction('/form')
                ->hasMethod('puT')
                ->hasSpoofMethod('Put');
        })
        ->assertOk();
});

it('can find enc type', function (): void {
    $this->get('form')
        ->assertFormExists('#form1', static function (AssertForm $form): void {
            $form->hasEnctype('multipart/form-data');
        })
        ->assertOk();
});

it('can find inputs', function (): void {
    $this->get('form')
        ->assertFormExists('#form2', static function (AssertForm $form): void {
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

it('can detect a missing input', function (): void {
    $this->get('form')
        ->assertFormExists('#form2', static function (AssertForm $form): void {
            $form->hasAction('/form')
                ->hasMethod('post')
                ->doesntContainInput([
                    'name' => 'last_name',
                ]);
        })->assertOk();
});

it('can ignore an input outside the form ', function (): void {
    $this->get('form')
        ->assertFormExists('#form2', static function (AssertForm $form): void {
            $form->hasAction('/form')
                ->hasMethod('post')
                ->doesntContainInput([
                    'name' => 'outside',
                ]);
        })->assertOk();
});

it('can test a textarea', function (): void {
    $this->get('form')
        ->assertFormExists(static function (AssertForm $form): void {
            $form->containsTextarea([
                'name' => 'comment',
                'value' => 'foo',
            ]);
        })->assertOk();
});

it('can test a textarea is required', function (): void {
    $this->get('form')
        ->assertFormExists(static function (AssertForm $form): void {
            $form->findTextarea(static function (AssertElement $textArea): void {
                $textArea->hasRequired();
            });
        })->assertOk();
});

it('can test a textarea has required true', function (): void {
    $this->get('form')
        ->assertFormExists(static function (AssertForm $form): void {
            $form->contains('textarea', [
                'required' => true,
            ]);
        })->assertOk();
});

it('can find no inputs with required', function (): void {
    $this->get('form')
        ->assertFormExists(static function (AssertForm $form): void {
            $form->doesntContain('input', [
                'required' => true,
            ]);
        })->assertOk();
});

it('can find a button', function (): void {
    $this->get('form')
        ->assertFormExists('#form2', static function (AssertForm $form): void {
            $form->containsButton([
                'type' => 'submit',
            ]);
        })->assertOk();
});

it('can check arbitrary attributes', function (): void {
    $this->get('form')
        ->assertFormExists(static function (AssertForm $form): void {
            $form->hasXData('foo');
        })->assertOk();
});

it('can check arbitrary children', function (): void {
    $this->get('form')
        ->assertFormExists(static function (AssertForm $form): void {
            $form->containsLabel([
                'for' => 'comment',
            ]);
        })->assertOk();
});

it('can parse a datalist with options', function (): void {
    $this->get('form')
        ->assertFormExists('#form2', static function (AssertForm $form): void {
            $form->findDatalist('#skills', static function (AssertDataList $datalist): void {
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

it('requires that the selector for datalist is an id', function (): void {
    $this->get('form')
        ->assertFormExists('#form2', static function (AssertForm $form): void {
            $form->findDatalist('.my-datalist', static function (AssertDataList $datalist): void {
                $datalist->containsOption([
                    'value' => 'My first value',
                ]);
            });
        });
})->throws(AssertionFailedError::class,
    'Selectors for datalists must be an id, given: .my-datalist');
