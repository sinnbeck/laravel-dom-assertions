<?php

use PHPUnit\Framework\AssertionFailedError;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use Sinnbeck\DomAssertions\Asserts\AssertForm;
use Sinnbeck\DomAssertions\Asserts\AssertSelect;
use Sinnbeck\DomAssertions\TestHtml;

it('can assert on a raw html string', function (): void {
    TestHtml::make('<nav><a class="cta" href="https://example.com">Go</a></nav>')
        ->assertElementExists('nav > a', static function (AssertElement $assert): void {
            $assert->is('a');
            $assert->has('href', 'https://example.com');
            $assert->containsText('Go');
        });
});

it('can wrap a raw html string', function (): void {
    TestHtml::make('<option value="1">Option 1</option>')
        ->wrap('select', ['name' => 'things'])
        ->assertSelect('[name="things"]', static function (AssertSelect $select): void {
            $select->containsOption(['value' => '1', 'text' => 'Option 1']);
        });
});

it('escapes attribute values when wrapping', function (): void {
    TestHtml::make('<input name="q">')
        ->wrap('form', ['action' => '/search?a=1&b="2"'])
        ->assertForm(static function (AssertForm $form): void {
            $form->hasAction('/search?a=1&b="2"');
            $form->containsInput(['name' => 'q']);
        });
});

it('renders boolean attributes without a value when wrapping', function (): void {
    TestHtml::make('<option value="1">Option 1</option>')
        ->wrap('select', ['multiple' => true, 'name' => 'things'])
        ->assertSelect(static function (AssertSelect $select): void {
            $select->has('multiple');
            $select->has('name', 'things');
        });
});

it('renders attributes given as a list without a value when wrapping', function (): void {
    TestHtml::make('<option value="1">Option 1</option>')
        ->wrap('select', ['multiple', 'required'])
        ->assertSelect(static function (AssertSelect $select): void {
            $select->has('multiple');
            $select->has('required');
        });
});

it('skips attributes that are false or null when wrapping', function (): void {
    TestHtml::make('<option value="1">Option 1</option>')
        ->wrap('select', ['multiple' => false, 'disabled' => null, 'name' => 'things'])
        ->assertSelect(static function (AssertSelect $select): void {
            $select->doesntHave('multiple');
            $select->doesntHave('disabled');
            $select->has('name', 'things');
        });
});

it('allows alpine and livewire attribute names when wrapping', function (): void {
    TestHtml::make('<input name="q">')
        ->wrap('form', ['wire:submit.prevent' => 'save', 'x-on:click' => 'go', ':bound' => 'value'])
        ->assertForm(static function (AssertForm $form): void {
            $form->has('wire:submit.prevent', 'save');
            $form->has('x-on:click', 'go');
            $form->has(':bound', 'value');
        });
});

it('can wrap more than once', function (): void {
    TestHtml::make('<option value="1">Option 1</option>')
        ->wrap('select', ['name' => 'things'])
        ->wrap('form', ['method' => 'post'])
        ->assertForm(static function (AssertForm $form): void {
            $form->hasMethod('post');
            $form->findSelect('[name="things"]', static function (AssertSelect $select): void {
                $select->containsOption(['value' => '1']);
            });
        });
});

it('assertions are chainable after wrapping', function (): void {
    TestHtml::make('<option value="1">Option 1</option><option value="2" selected="selected">Option 2</option>')
        ->wrap('select')
        ->assertContainsElement('option', ['value' => '1', 'text' => 'Option 1'])
        ->assertDoesntExist('option[value="3"]')
        ->assertSelect(static function (AssertSelect $select): void {
            $select->hasValue('2');
        });
});

it('fails when wrapping an empty string', function (): void {
    TestHtml::make('')->wrap('select');
})->throws(AssertionFailedError::class, 'The html is empty!');

it('fails when the element name is invalid', function (): void {
    TestHtml::make('<option value="1">Option 1</option>')->wrap('<select>');
})->throws(AssertionFailedError::class, 'Invalid element name: "<select>"');

it('fails when an attribute name is invalid', function (): void {
    TestHtml::make('<option value="1">Option 1</option>')->wrap('select', ['na me' => 'things']);
})->throws(AssertionFailedError::class, 'Invalid attribute name: "na me"');

it('fails on an at-prefixed attribute name, which cannot survive parsing', function (): void {
    TestHtml::make('<input name="q">')->wrap('form', ['@click' => 'go']);
})->throws(AssertionFailedError::class, 'Invalid attribute name: "@click"');
