# DOM Assertions for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sinnbeck/laravel-dom-assertions.svg?style=flat-square)](https://packagist.org/packages/sinnbeck/laravel-dom-assertions)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sinnbeck/laravel-dom-assertions/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sinnbeck/laravel-dom-assertions/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/sinnbeck/laravel-dom-assertions/fix-php-cs.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/sinnbeck/laravel-dom-assertions/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sinnbeck/laravel-dom-assertions.svg?style=flat-square)](https://packagist.org/packages/sinnbeck/laravel-dom-assertions)
[![Laravel Compatibility](https://badge.laravel.cloud/badge/sinnbeck/laravel-dom-assertions?style=flat)](https://packagist.org/packages/sinnbeck/laravel-dom-assertions)


This package provides some extra assertion helpers to use in HTTP Tests. If you have ever needed more control over your view assertions than `assertSee`, `assertSeeInOrder`, `assertSeeText`, `assertSeeTextInOrder`, `assertDontSee`, and `assertDontSeeText` then this is the package for you.

## Installation

You can install the package via composer:

> Version 3.x and above requires PHP 8.1+ and Laravel 10+.

```bash
composer require sinnbeck/laravel-dom-assertions --dev
```

> **Note:** If you're using PHP 8.0 or Laravel 9, please use version 2.x:
 
```bash
 composer require sinnbeck/laravel-dom-assertions:^2.0 --dev
 ```


## Table of contents

- [Asserting on elements](#asserting-on-elements)
- [Asserting on forms](#asserting-on-forms)
- [Asserting on selects](#asserting-on-selects)
- [Asserting on datalists](#asserting-on-datalists)
- [Asserting on text](#asserting-on-text)
- [Quick existence checks](#quick-existence-checks)
- [Usage with Livewire](#usage-with-livewire)
- [Usage with Blade views and components](#usage-with-blade-views-and-components)
- [Method reference](#method-reference)
- [Rector rules](#rector-rules)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Asserting on elements

Use `assertElementExists()` (or its alias `assertElement()`) on a test response to assert against the DOM. This package assumes a valid HTML document and will wrap your markup in `<html>`, `<head>`, and `<body>` tags if they are missing.

Called with no arguments, it simply asserts that a `<body>` element was parsed:

```php
$this->get('/some-route')
    ->assertElementExists();
```

Pass a CSS selector as the first argument to target a specific element:

```php
$this->get('/some-route')
    ->assertElementExists('#nav');
```

The second argument is a closure that receives an `AssertElement` instance, which is where all of the fluent assertions below live.

### Asserting the element type

```php
$this->get('/some-route')
    ->assertElementExists('#overview', function (AssertElement $assert) {
        $assert->is('div');
    });
```

### Asserting attributes

Assert that an attribute is present, optionally with a specific value:

```php
$this->get('/some-route')
    ->assertElementExists('#overview', function (AssertElement $assert) {
        $assert->has('x-data', '{foo: 1}');
    });
```

Or assert that it is absent:

```php
$this->get('/some-route')
    ->assertElementExists('#overview', function (AssertElement $assert) {
        $assert->doesntHave('x-data', '{foo: 2}');
    });
```

### Asserting on children

Confirm a child element exists:

```php
$this->get('/some-route')
    ->assertElementExists('#overview', function (AssertElement $assert) {
        $assert->contains('div');
    });
```

Narrow it down with a CSS selector:

```php
$this->get('/some-route')
    ->assertElementExists('#overview', function (AssertElement $assert) {
        $assert->contains('div:nth-of-type(3)');
    });
```

Assert that the child carries certain attributes:

```php
$this->get('/some-route')
    ->assertElementExists('#overview', function (AssertElement $assert) {
        $assert->contains('li.list-item', ['x-data' => 'foobar']);
    });
```

Assert it appears an exact number of times by passing a count as the final argument:

```php
$this->get('/some-route')
    ->assertElementExists('#overview', function (AssertElement $assert) {
        $assert->contains('li.list-item', ['x-data' => 'foobar'], 3);
    });
```

When you only care about the count, drop the attributes argument:

```php
$this->get('/some-route')
    ->assertElementExists('#overview', function (AssertElement $assert) {
        $assert->contains('li.list-item', 3);
    });
```

Or assert that no matching child exists:

```php
$this->get('/some-route')
    ->assertElementExists('#overview', function (AssertElement $assert) {
        $assert->doesntContain('li.list-item', ['x-data' => 'foobar']);
    });
```

### Drilling into a child

`find()` selects the first matching child and lets you assert against it. Pass a closure to receive a fresh `AssertElement` for that child:

```php
$this->get('/some-route')
    ->assertElementExists('#overview', function (AssertElement $assert) {
        $assert->find('li:nth-of-type(3)', function (AssertElement $element) {
            $element->is('li');
        });
    });
```

To assert against *every* matching element rather than just the first, use `each()`:

```php
$this->get('/some-route')
    ->assertElementExists('#overview', function (AssertElement $assert) {
        $assert->each('li', function (AssertElement $element) {
            $element->has('class', 'list-item');
        });
    });
```

Because each `find()` hands you another `AssertElement`, you can drill arbitrarily deep into the DOM:

```php
$this->get('/some-route')
    ->assertElementExists(function (AssertElement $element) {
        $element->find('div', function (AssertElement $element) {
            $element->is('div');

            $element->find('p', function (AssertElement $element) {
                $element->is('p');
                $element->find('#label', fn (AssertElement $element) => $element->is('span'));
            });

            $element->find('p:nth-of-type(2)', function (AssertElement $element) {
                $element->is('p');
                $element->find('.sub-header', fn (AssertElement $element) => $element->is('h4'));
            });
        });
    });
```

### Magic methods

Element type and attribute assertions have convenient magic-method shortcuts:

```php
$assert->isDiv();                              // is('div')
$assert->hasXData('{foo: 1}');                 // has('x-data', '{foo: 1}')
$assert->containsDiv(['class' => 'foo'], 3);   // contains('div', ['class' => 'foo'], 3)
$assert->doesntContainSpan(['class' => 'foo']); // doesntContain('span', ['class' => 'foo'])
$assert->findDiv(fn (AssertElement $el) => $el->isDiv()); // find('div', ...)
```

## Asserting on forms

Forms support every element assertion above, plus a handful of form-specific helpers. Use `assertFormExists()` (alias `assertForm()`), which targets the first `<form>` on the page by default:

```php
$this->get('/some-route')
    ->assertFormExists();
```

Pass a selector to target a specific form:

```php
$this->get('/some-route')
    ->assertFormExists('#users-form');
```

The closure receives an `AssertForm` instance. Assert on the action and method:

```php
$this->get('/some-route')
    ->assertFormExists('#form1', function (AssertForm $form) {
        $form->hasAction('/logout')
            ->hasMethod('post');
    });
```

Omit the selector entirely and pass the closure directly to target the first form:

```php
$this->get('/some-route')
    ->assertFormExists(function (AssertForm $form) {
        $form->hasAction('/logout')->hasMethod('post');
    });
```

### CSRF tokens and method spoofing

```php
$this->get('/some-route')
    ->assertFormExists(function (AssertForm $form) {
        $form->hasAction('/update-user')
            ->hasMethod('post')
            ->hasCSRF()
            ->hasSpoofMethod('PUT');
    });
```

Any method other than `GET` or `POST` is automatically treated as a spoofed method, so this is equivalent to calling `hasSpoofMethod('PUT')`:

```php
$this->get('/some-route')
    ->assertFormExists(function (AssertForm $form) {
        $form->hasMethod('PUT');
    });
```

Arbitrary attributes are supported too, including via magic methods:

```php
$this->get('/some-route')
    ->assertFormExists(function (AssertForm $form) {
        $form->has('x-data', 'foo')
            ->hasEnctype('multipart/form-data'); // magic method
    });
```

### Inputs, textareas, and buttons

```php
$this->get('/some-route')
    ->assertFormExists(function (AssertForm $form) {
        $form->containsInput(['name' => 'first_name', 'value' => 'Gunnar'])
            ->containsTextarea(['name' => 'comment', 'value' => '...']);
    });
```

You can also assert on arbitrary children, or their absence:

```php
$this->get('/some-route')
    ->assertFormExists(function (AssertForm $form) {
        $form->contains('label', ['for' => 'username'])
            ->containsButton(['type' => 'submit']) // magic method
            ->doesntContain('label', ['for' => 'password']);
    });
```

## Asserting on selects

`findSelect()` takes a selector and a closure that receives an `AssertSelect` instance. Assert on the select's own attributes:

```php
$this->get('/some-route')
    ->assertFormExists(function (AssertForm $form) {
        $form->findSelect('select:nth-of-type(2)', function (AssertSelect $select) {
            $select->has('name', 'country');
        });
    });
```

Assert on its options — one at a time with `containsOption()`, or several at once with `containsOptions()`:

```php
$this->get('/some-route')
    ->assertFormExists(function (AssertForm $form) {
        $form->findSelect('select:nth-of-type(2)', function (AssertSelect $select) {
            $select->containsOption([
                'x-data' => 'none',
                'value'  => 'none',
                'text'   => 'None',
            ])->containsOptions(
                ['value' => 'dk', 'text' => 'Denmark'],
                ['value' => 'us', 'text' => 'USA'],
            );
        });
    });
```

Assert on the selected value, or on multiple selected values for a multi-select:

```php
$this->get('/some-route')
    ->assertFormExists('#form1', function (AssertForm $form) {
        $form->findSelect('select', function (AssertSelect $select) {
            $select->hasValue('da');
            $select->hasValues(['da', 'en']);
        });
    });
```

## Asserting on datalists

Datalists work like selects via `findDatalist()`, which provides an `AssertDatalist` instance. The selector must be either `datalist` or an id such as `#skills`:

```php
$this->get('/some-route')
    ->assertFormExists('#form1', function (AssertForm $form) {
        $form->findDatalist('#skills', function (AssertDatalist $list) {
            $list->containsOptions(
                ['value' => 'PHP'],
                ['value' => 'Javascript'],
            );
        });
    });
```

## Asserting on text

`containsText()` and `doesntContainText()` assert against an element's text content:

```php
$this->get('/some-route')
    ->assertElementExists('#overview', function (AssertElement $assert) {
        $assert->containsText('Hello World');
    });
```

### Whitespace normalisation

By default these comparisons match text exactly as it appears in the DOM. Templates often introduce a lot of incidental whitespace — indented Blade, multi-line content, `\r\n` line endings — so you can collapse and trim it instead.

Enable it for a single call:

```php
$assert->containsText('Hello World', ignoreCase: false, normalizeWhitespace: true);
```

Enable it globally from `TestCase::setUp()` or `AppServiceProvider::boot()`:

```php
config()->set('dom-assertions.normalize_whitespace', true);
```

Or publish the config file if you prefer:

```bash
php artisan vendor:publish --tag=dom-assertions-config
```

This creates `config/dom-assertions.php`:

```php
return [
    /*
    | When enabled, text comparisons performed by `containsText` and
    | `doesntContainText` will collapse consecutive whitespace and trim
    | vertical whitespace from both the needle and haystack by default.
    |
    | This can still be overridden per-call by passing an explicit boolean
    | as the third argument to those assertions.
    */
    'normalize_whitespace' => true,
];
```

When `normalizeWhitespace` is left as `null`, it falls back to this config value. With the global default on, pass `normalizeWhitespace: false` to force strict matching for a single assertion.

## Quick existence checks

For simple checks where a full closure is overkill, use `assertContainsElement()` and `assertDoesntExist()` directly on the response. `assertContainsElement()` optionally accepts an array of expected attributes:

```php
$this->get('/some-route')
    ->assertContainsElement('#content')
    ->assertContainsElement('div.banner', ['text' => 'Successfully deleted', 'data-status' => 'success'])
    ->assertDoesntExist('div.not-here');
```

When a check fails, chain `ddContent()` to dump the parsed page and see what was actually rendered:

```php
$this->blade('<x-some-blade>')
    ->assertContainsElement('#content')
    ->ddContent();
```

> [!TIP]
> These methods are shared across the response, view, and component macros, so they are available anywhere this package can be used.

## Usage with Livewire

Livewire's testing helpers return Laravel's `TestResponse`, so everything works without any changes:

```php
Livewire::test(UserForm::class)
    ->assertElementExists('form', function (AssertElement $form) {
        $form->find('#submit', function (AssertElement $assert) {
            $assert->is('button');
            $assert->has('text', 'Submit');
        })->contains('[wire\:model="name"]', 1);
    });
```

## Usage with Blade views and components

Test a Blade view directly:

```php
$this->view('navigation')
    ->assertElementExists('nav > ul', function (AssertElement $ul) {
        $ul->contains('li', ['class' => 'active']);
    });
```

Or a Blade component:

```php
$this->component(Navigation::class)
    ->assertElementExists('nav > ul', function (AssertElement $ul) {
        $ul->contains('li', ['class' => 'active']);
    });
```

## Method reference

### Element methods (`AssertElement`)

| Method | Description |
|--------|-------------|
| `is($type)` | Assert the element is of a given type (`div`, `span`, …). |
| `isDiv()` | Magic method. Same as `is('div')`. |
| `has($attribute, $value = null)` | Assert the element has an attribute, optionally with a given value. |
| `hasXData('foo')` | Magic method. Same as `has('x-data', 'foo')`. |
| `doesntHave($attribute, $value = null)` | Assert the element does not have the attribute/value. |
| `contains($selector, $attributes = [], $count = null)` | Assert a child element exists, optionally with attributes and/or an exact count. |
| `containsDiv(['class' => 'foo'], 3)` | Magic method. Same as `contains('div', ['class' => 'foo'], 3)`. |
| `doesntContain($selector, $attributes = [])` | Assert no matching child exists. |
| `doesntContainDiv(['class' => 'foo'])` | Magic method. Same as `doesntContain('div', ['class' => 'foo'])`. |
| `containsText($needle, $ignoreCase = false, $normalizeWhitespace = null)` | Assert the element's text contains a string. `$normalizeWhitespace` defaults to the `dom-assertions.normalize_whitespace` config value when `null`. |
| `doesntContainText($needle, $ignoreCase = false, $normalizeWhitespace = null)` | Assert the element's text does not contain a string. Same `$normalizeWhitespace` behaviour. |
| `find($selector, $callback)` | Drill into the first matching child and receive a new `AssertElement`. |
| `findDiv(fn (AssertElement $el) => …)` | Magic method. Same as `find('div', …)`. |
| `each($selector, $callback)` | Run the callback against every matching child. |

### Form methods (`AssertForm`)

| Method | Description |
|--------|-------------|
| `hasAction($url)` | Assert the form posts to a given action. |
| `hasMethod($method)` | Assert the form uses a given method (non-GET/POST forwards to `hasSpoofMethod`). |
| `hasSpoofMethod($method)` | Assert the form contains a spoofed `_method` field. |
| `hasCSRF()` | Assert the form contains a CSRF token. |
| `containsInput($attributes)` | Assert a matching `<input>` exists. |
| `containsTextarea($attributes)` | Assert a matching `<textarea>` exists. |
| `findSelect($selector, $callback)` | Drill into a `<select>` and receive an `AssertSelect`. |
| `findDatalist($selector, $callback)` | Drill into a `<datalist>` and receive an `AssertDatalist`. |

All `AssertElement` methods are also available on forms.

### Select methods (`AssertSelect`)

| Method | Description |
|--------|-------------|
| `hasValue($value)` | Assert the select's selected value. |
| `hasValues($values)` | Assert the selected values of a multiple select. |
| `containsOption($attributes)` | Assert a single option with the given attributes exists. |
| `containsOptions(...$attributes)` | Assert several options exist (one array per option). |

## Rector rules

This package ships [Rector](https://getrector.com/) rules to keep your assertions consistent as the package evolves.

| Rule | Description |
|------|-------------|
| `AssertElementToAssertContainsElementRule` | Converts verbose `assertElement()` closures into flat `assertContainsElement()` chains. |

Register a rule in your `rector.php`:

```php
use Rector\Config\RectorConfig;
use Sinnbeck\DomAssertions\Rector\Rules\AssertElementToAssertContainsElementRule;

return RectorConfig::configure()
    ->withRules([
        AssertElementToAssertContainsElementRule::class,
    ]);
```

### `AssertElementToAssertContainsElementRule`

Converts `assertElement()` calls whose closures only use `find`, `contains`, `containsText`, or `has` into flat `assertContainsElement()` chains:

```php
// Before
$response->assertElement('#content', function (AssertElement $element) {
    $element->find('h1', function (AssertElement $element) {
        $element->containsText('Hello World');
    });
    $element->contains('p', ['class' => 'foo']);
});

// After
$response->assertContainsElement('#content h1', ['text' => 'Hello World'])
         ->assertContainsElement('#content p', ['class' => 'foo']);
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
- [Jack Bayliss](https://github.com/jackbayliss)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
