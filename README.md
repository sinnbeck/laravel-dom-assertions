# Laravel Dom Assertions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sinnbeck/laravel-dom-assertions.svg?style=flat-square)](https://packagist.org/packages/sinnbeck/laravel-dom-assertions)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/sinnbeck/laravel-dom-assertions/run-tests?label=tests)](https://github.com/sinnbeck/laravel-dom-assertions/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/sinnbeck/laravel-dom-assertions/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/sinnbeck/laravel-dom-assertions/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sinnbeck/laravel-dom-assertions.svg?style=flat-square)](https://packagist.org/packages/sinnbeck/laravel-dom-assertions)

This package provides some extra assertion helpers to use in HTTP Tests. If you have ever needed more control over your view assertions than `assertSee`, `assertSeeInOrder`, `assertSeeText`, `assertSeeTextInOrder`, `assertDontSee`, and `assertDontSeeText`, then this is the package for you.

## Installation

You can install the package via composer:

```bash
composer require sinnbeck/laravel-dom-assertions --dev
```

## Usage

### Testing the DOM
When calling a route in a test you might want to make sure that the view contains certain elements. To test this you can use the `->assertElement()` method on the test response.
The following will ensure that there is a body tag in the parsed response. Be aware that this package assumes a proper html structure and will wrap your html in a html and body tag if one is missing!
```php
$this->get('/some-route')
    ->assertElement();
```
In case you want to get a specific element on the page, you can supply a css selector as the first argument to get a specific one.
```php
$this->get('/some-route')
    ->assertElement('#nav');
```
The second argument of `->assertElement()` is a closure that receives an instance of `\Sinnbeck\DomAssertions\Asserts\ElementAssert`. This allows you to assert things about the element itself. Here we are asserting that the element is a `div`.

```php
$this->get('/some-route')
    ->assertElement('#overview', function (ElementAssert $assert) {
        $assert->is('div');
    });
```
Just like with forms you can assert that certain attributes are present
```php
$this->get('/some-route')
    ->assertElement('#overview', function (ElementAssert $assert) {
        $assert->has('x-data', '{foo: 1}');
    });
```
You can also ensure that certain children exist.
```php
$this->get('/some-route')
    ->assertElement('#overview', function (ElementAssert $assert) {
        $assert->contains('div');
    });
```
If you need to be more specific you can use a css selector.
```php
$this->get('/some-route')
    ->assertElement('#overview', function (ElementAssert $assert) {
        $assert->contains('div:nth-of-type(3)');
    });
```
You can also check that the child element has certain attributes.
```php
$this->get('/some-route')
    ->assertElement('#overview', function (ElementAssert $assert) {
        $assert->contains('li.list-item', [
            'x-data' => 'foobar'
        ]);
    });
```
or ensure that certain children does not exist
```php
$this->get('/some-route')
    ->assertElement('#overview', function (ElementAssert $assert) {
        $assert->doesntContain('li.list-item', [
            'x-data' => 'foobar'
        ]);
    });
```
Contains also allow a third argument to specify how many times the element should be matched.
```php
$this->get('/some-route')
    ->assertElement('#overview', function (ElementAssert $assert) {
        $assert->contains('li.list-item', [
            'x-data' => 'foobar'
        ], 3);
    });
```
If you just want to check for the element type you can leave out the second argument.
```php
$this->get('/some-route')
    ->assertElement('#overview', function (ElementAssert $assert) {
        $assert->contains('li.list-item', 3);
    });
```
You can also find a certain element and do assertions on it. Be aware that it will only check the first matching element.
```php
$this->get('/some-route')
    ->assertElement('#overview', function (ElementAssert $assert) {
        $assert->find('li.list-item');
    });
```
You can add a closure as the second argument which receives an instance of `\Sinnbeck\DomAssertions\Asserts\ElementAssert`.
```php
$this->get('/some-route')
    ->assertElement('#overview', function (ElementAssert $assert) {
        $assert->find('li.nth-of-type(3)', function (ElementAssert $element) {
            $this->is('li');
        })
    });
```

This means that you can infinitely assert down the dom structure.
```php
$this->get('/some-route')
    ->assertElement(function (ElementAssert $element) {
        $element->find('div', function (ElementAssert $element) {
            $element->is('div');
            $element->contains('p', function (ElementAssert $element) {
                $element->is('p');
                $element->contains('#label', function (ElementAssert $element) {
                    $element->is('span');
                });
            });
            $element->contains('p:nth-of-type(2)', function (ElementAssert $element) {
                $element->is('p');
                $element->contains('.sub-header', function (ElementAssert $element) {
                    $element->is('h4');
                });
            });
        });
    });
```

### Testing forms
Testing forms allows using all the dom asserts from above, but has a few special helpers to help test for forms.
Instead of using `->assertElement()` we will use `->assertForm()` method on the test response.
```php
$this->get('/some-route')
    ->assertForm();
```
The `->assertForm()` method will check the first form it finds. In case you have more than one form, and want to use a different form that the first, you can supply a css selector as the first argument to get a specific one.
```php
$this->get('/some-route')
    ->assertForm('#users-form');
```
If there is more than one hit, it will return the first matching form.
```php
$this->get('/some-route')
    ->assertForm(null, 'nav .logout-form');
```
The second argument of `->assertForm()` is a closure that receives an instance of `\Sinnbeck\DomAssertions\Asserts\FormAssert`. This allows you to assert things about the form itself. Here we are asserting that it has a certain action and method
```php
$this->get('/some-route')
    ->assertForm('#form1', function (FormAssert $form) {
        $form->hasAction('/logout')
            ->hasMethod('post');
    });
```
If you leave out the css selector, it will automatically default to finding the first form on the page
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->hasAction('/logout')
            ->hasMethod('post');
    });
```

You can also check for csrf and method spoofing
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->hasAction('/update-user')
            ->hasMethod('post')
            ->hasCSRF()
            ->hasSpoofMethod('PUT');
    });
```
Checking for methods other than GET and POST will automatically forward the call to `->hasSpoofMethod()`
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->hasMethod('PUT');
    });
```
Or even arbitrary attributes
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->has('x-data', 'foo')
        $form->hasEnctype('multipart/form-data'); //it also works with magic methods
    });
```

You can also easily test for inputs or text areas 
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->containsInput([
            'name' => 'first_name',
            'value' => 'Gunnar',
        ])
        ->containsTextarea([
            'name' => 'comment',
            'value' => '...',
        ]);
    });
```
Or arbitrary children
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->contains('label', [
            'for' => 'username',
        ])
        ->containsButton([ //or use a magic method
            'type' => 'submit',
        ]);
    });
```
You can also ensure that certain children does not exist.
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->doesntContain('label', [
            'for' => 'username',
        ]);
    });
```
Testing for selects is also easy and works a bit like the `assertForm()`. It takes a selector as the first argument, and closure as the second argument. The second argument returns an instance of `\Sinnbeck\DomAssertions\Asserts\SelectAssert`. This can be used to assert that the select has certain attributes.
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->findSelect('select:nth-of-type(2)', function (SelectAssert $selectAssert) {
            $selectAssert->has('name', 'country')
        });
    });
```
You can also assert that it has certain options. You can either check for one specific or an array of options
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->findSelect(function (SelectAssert $selectAssert) {
            $selectAssert->containsOption([
                [
                    'x-data' => 'none',
                    'value'  => 'none',
                    'text'   => 'None',
                ]
            ])
            ->containsOptions(
                [
                    'value' => 'dk',
                    'text'  => 'Denmark',
                ],
                [
                    'value' => 'us',
                    'text'  => 'USA',
                ],
            );
        }, 'select:nth-of-type(2)');
    });
```
You can check if a select has a value.
```php
$this->get('/some-route')
        ->assertForm('#form1', function (FormAssert $form) {
            $form->findSelect('select', function (SelectAssert $selectAssert) {
                $selectAssert->hasValue('da');
            });
        });
```


You can also check selects with multiple values
```php
$this->get('/some-route')
        ->assertForm('#form1', function (FormAssert $form) {
            $form->findSelect('select', function (SelectAssert $selectAssert) {
                $selectAssert->hasValues(['da', 'en']);
            });
        });
```

## Testing this package

```bash
vendor/bin/pest
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [René Sinnbeck](https://github.com/sinnbeck)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.