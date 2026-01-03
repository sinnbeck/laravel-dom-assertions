<?php

use PHPUnit\Framework\AssertionFailedError;
use Sinnbeck\DomAssertions\Asserts\AssertElement;

it('containsHtml passes when literal HTML is present', function () {
    $this->view('nesting')
        ->assertElement('div', function (AssertElement $assert) {
            $assert->containsHtml('<span></span>');
        });
});

it('containsHtml fails when literal HTML is not present', function () {
    $this->view('nesting')
        ->assertElement('div', function (AssertElement $assert) {
            $assert->containsHtml('<strong>nope</strong>');
        });
})->throws(AssertionFailedError::class, 'Failed asserting that HTML contains: <strong>nope</strong>');

it('doesntContainHtml passes when literal HTML is not present', function () {
    $this->view('nesting')
        ->assertElement('div', function (AssertElement $assert) {
            $assert->doesntContainHtml('<strong>nope</strong>');
        });
});

it('doesntContainHtml fails when literal HTML is present', function () {
    $this->view('nesting')
        ->assertElement('div', function (AssertElement $assert) {
            $assert->doesntContainHtml('<span></span>');
        });
})->throws(AssertionFailedError::class, 'Failed asserting that HTML does not contain: <span></span>');

it('containsHtml works for various attributes and tags', function () {
    $this->view('media')
        ->assertElement('#gallery', function (AssertElement $assert) {
            $assert->containsHtml('src="/images/cat.jpg"');
            $assert->containsHtml('alt="A cat"');
            $assert->containsHtml('data-id="123"');
            $assert->containsHtml('type="image/webp"');
            $assert->containsHtml('id="gallery"');
            $assert->containsHtml('data-testid="gallery-1"');
            $assert->containsHtml('aria-hidden="false"');
            $assert->containsHtml('<picture>');
        });
});

it('doesntContainHtml works for absent or wrong attributes and tags', function () {
    $this->view('media')
        ->assertElement('#gallery', function (AssertElement $assert) {
            $assert->doesntContainHtml('src="/images/hamster.jpg"');
            $assert->doesntContainHtml('data-id="000"');
            $assert->doesntContainHtml('alt="A hamster"');
            $assert->doesntContainHtml('type="image/png"');
            $assert->doesntContainHtml('charset="UTF-16"');
            $assert->doesntContainHtml('<svg');
        });
    $this->view('media')
        ->assertElement('body', function (AssertElement $assert) {
            $assert->doesntContainHtml('<iframe');
        });
});

it('containsHtml respects partial matches and whitespace', function () {
    $this->view('nesting')
        ->assertElement('p.foo.bar', function (AssertElement $assert) {
            $assert->containsHtml('<span>Bar</span>');
        });
});

it('containsHtml can match script tag content', function () {
    $this->view('media')
        ->assertElement('script#config', function (AssertElement $assert) {
            $assert->containsHtml('"feature": "media"');
            $assert->containsHtml('"enabled": true');
        });
});

it('containsHtml supports boolean-like attributes present', function () {
    $this->view('nesting')
        ->assertElement('meta[charset]', function (AssertElement $assert) {
            $assert->containsHtml('charset="UTF-8"');
        });
});

it('containsHtml can match attribute without quotes if normalized', function () {
    $this->view('nesting')
        ->assertElement('meta[name="viewport"]', function (AssertElement $assert) {
            $assert->containsHtml('name="viewport"');
            $assert->containsHtml('content="width=device-width');
        });
});

it('containsHtml can find stylesheet link href', function () {
    $this->view('media')
        ->assertElement('head', function (AssertElement $assert) {
            $assert->containsHtml('<link href="/css/app.css" rel="stylesheet">');
        });
});

it('doesntContainHtml can fail on hidden substring', function () {
    $this->view('media')
        ->assertElement('#gallery', function (AssertElement $assert) {
            $assert->doesntContainHtml('hidden');
        });
})->throws(AssertionFailedError::class, 'Failed asserting that HTML does not contain: hidden');
