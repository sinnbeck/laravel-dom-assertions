# Laravel Dom Assertions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sinnbeck/laravel-dom-assertions.svg?style=flat-square)](https://packagist.org/packages/sinnbeck/laravel-dom-assertions)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/sinnbeck/laravel-dom-assertions/run-tests?label=tests)](https://github.com/sinnbeck/laravel-dom-assertions/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/sinnbeck/laravel-dom-assertions/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/sinnbeck/laravel-dom-assertions/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sinnbeck/laravel-dom-assertions.svg?style=flat-square)](https://packagist.org/packages/sinnbeck/laravel-dom-assertions)

This package provides some extra assertion helpers to use in HTTP Tests. If you have ever needed more control over your view assertions than `assertSee`, `assertSeeInOrder`, `assertSeeText`, `assertSeeTextInOrder`, `assertDontSee`, and `assertDontSeeText`, then this is the package for you.

## Installation

You can install the package via composer:

```bash
composer require sinnbeck/laravel-dom-assertions
```

## Usage

### Testing forms
Let's say you have a view with a form. We want to ensure that it has the correct method and action. You can then use the `->assertForm()` method to assert that the form has the provided attributes.
```php
$this->get('/some-route')
    ->assertForm();
```
The `->assertForm()` method will check the first form it finds. In case you have more than one form, and want to use a different form that the first, you can supply either a zero based index of the form as the second argument
```php
$this->get('/some-route')
    ->assertForm(null, 1); //get the second form on the page
```
For even more control you can give it a css selector. It will always use the first match.
```php
$this->get('/some-route')
    ->assertForm(null, 'nav .logout-form'); //get a specific form in nav by class
```
The first argument of `->assertForm()` is a closure that recieves an instance of `FormAssert`. This allows you to assert things about the form itself. Here we are asserting that it has a certain action and method
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
Or even arbitrary attributes
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->has('x-data', 'foo')
        $form->hasEncType('multipart/form-data'); //it also works with magic methods
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
Testing for selects is also easy but require a bit of special syntax. First of it requires a selector as the second argument, to get the correct select. It will only check inside the already selected form. Secondly it uses a closure just like the form, which allows some better assertions.
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->containsSelect(function (SelectAssert $selectAssert) {
            $selectAssert->has('name', 'country')
        }, 'select:nth-of-type(2)');
    });
```
You can also assert that it has certain options. You can either check for one specific or an array of options
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->containsSelect(function (SelectAssert $selectAssert) {
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
It also works with callables if you prefer that syntax
```php
$this->get('/some-route')
    ->assertForm(function (FormAssert $form) {
        $form->containsSelect(function (SelectAssert $selectAssert) {
            $selectAssert->containsOption(function (OptionAssert $optionAssert) {
                $optionAssert->hasValue('none');
                $optionAssert->hasText('None');
                $optionAssert->hasXData('none');
            })
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
    });
```
## Testing

```bash
composer test
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