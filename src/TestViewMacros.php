<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions;

use Closure;
use DOMException;
use Illuminate\Support\Str;
use Illuminate\Testing\TestView;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use Sinnbeck\DomAssertions\Asserts\AssertForm;
use Sinnbeck\DomAssertions\Support\CompareAttributes;
use Sinnbeck\DomAssertions\Support\DomParser;

/**
 * @internal
 *
 * @mixin TestView
 */
class TestViewMacros
{
    public function assertHtml5()
    {
        return function () {
            /** @var TestView $this */
            Assert::assertNotEmpty(
                (string) $this,
                'The view is empty!'
            );

            try {
                $parser = DomParser::new((string) $this);
            } catch (DOMException $exception) {
                Assert::fail($exception->getMessage());
            }

            Assert::assertEquals(
                'html',
                $parser->getDocType(),
                'Not a html5 doctype!'
            );

            return $this;
        };
    }

    public function assertElementExists(): Closure
    {
        return function ($selector = 'body', $callback = null): TestView {
            /** @var TestView $this */
            Assert::assertNotEmpty(
                (string) $this,
                'The view is empty!'
            );

            try {
                $parser = DomParser::new((string) $this);
            } catch (DOMException $exception) {
                Assert::fail($exception->getMessage());
            }

            if ($selector instanceof Closure) {
                $callback = $selector;
                $selector = 'body';
            }

            if (is_string($selector)) {
                $element = $parser->query($selector);
            } else {
                Assert::fail('Invalid selector!');
            }

            Assert::assertNotNull($element, sprintf('No element found with selector: %s', $selector));

            if ($callback) {
                $callback(new AssertElement((string) $this, $element));
            }

            return $this;
        };
    }

    public function assertElementDoesNotExist(): Closure
    {
        return function ($selector, $attributes = []): TestView {
            /** @var TestView $this */
            Assert::assertNotEmpty(
                (string)$this,
                'The view is empty!'
            );

            try {
                $parser = DomParser::new((string)$this);
            } catch (DOMException $exception) {
                Assert::fail($exception->getMessage());
            }

            if (is_string($selector)) {
                $element = $parser->query($selector);
            } else {
                Assert::fail('Invalid selector!');
            }

            (new AssertElement((string)$this, $element))
                ->doesntContain($selector, $attributes);

            return $this;
        };
    }

    public function assertFormExists(): Closure
    {
        return function ($selector = 'form', $callback = null): TestView {
            /** @var TestView $this */
            Assert::assertNotEmpty(
                (string) $this,
                'The view is empty!'
            );

            try {
                $parser = DomParser::new((string) $this);
            } catch (DOMException $exception) {
                Assert::fail($exception->getMessage());
            }

            if ($selector instanceof Closure) {
                $callback = $selector;
                $selector = 'form';
            }

            if (is_string($selector)) {
                $form = $parser->query($selector);
            } else {
                Assert::fail('Invalid selector!');
            }

            Assert::assertNotNull(
                $form,
                sprintf('No form was found with selector "%s"', $selector)
            );
            Assert::assertEquals(
                'form',
                $form->nodeName,
                'Element is not of type form!');

            if ($callback) {
                $callback(new AssertForm((string) $this, $form));
            }

            return $this;
        };
    }

    public function assertFormDoesNotExist(): Closure
    {
        return function ($selector = 'form', $method = null, $action = null): TestView {
            /** @var TestView $this */
            Assert::assertNotEmpty(
                (string)$this,
                'The view is empty!'
            );

            try {
                $parser = DomParser::new((string)$this);
            } catch (DOMException $exception) {
                Assert::fail($exception->getMessage());
            }

            if (!is_string($selector)) {
                Assert::fail('Invalid selector!');
            }

            $allForms = $parser->queryAll($selector);

            if (!$method && !$action && $allForms->length > 0) {
                $failMessage = $selector === 'form'
                    ? 'A form exists in the response'
                    : sprintf('Found a matching form for the selector "%s"', $selector);
                Assert::fail($failMessage);
            }

            foreach ($allForms as $form) {
                $parser->setRoot($form);

                $isMatchForMethod = false;
                if ($method) {
                    $sanitizedMethodExpected = Str::of($method)->trim()->upper()->toString();
                    $sanitizedMethodActual = Str::of($parser->getAttributeForRoot('method'))->trim()->upper()->toString();

                    if ($sanitizedMethodExpected === 'POST') {
                        $isMatchForMethod = CompareAttributes::compare('method', $sanitizedMethodExpected, $sanitizedMethodActual);
                        $hasHiddenInput = (bool)$parser->query('input[type=hidden][name=_method]');
                        $isMatchForMethod = $isMatchForMethod && !$hasHiddenInput;
                    } else {
                        $isMatchForSpoofMethod = CompareAttributes::compare('method', 'POST', $sanitizedMethodActual);
                        $isMatchForHiddenInput = (bool)$parser->query("input[type=hidden][name=_method][value={$sanitizedMethodExpected}]");
                        $isMatchForMethod = $isMatchForSpoofMethod && $isMatchForHiddenInput;
                    }
                }

                $isMatchForAction = false;
                if ($action) {
                    $sanitizedActionExpected = Str::of($action)->upper()->trim()->finish('/')->start('/')->toString();
                    $sanitizedActionActual = Str::of($parser->getAttributeForRoot('action'))->upper()->trim()->finish('/')->start('/')->toString();

                    $isMatchForAction = $sanitizedActionExpected === $sanitizedActionActual;
                }

                if ($action && $method) {
                    Assert::assertFalse($isMatchForMethod && $isMatchForAction, sprintf('A form exists with method "%s" and action "%s"', $method, $action));
                } elseif ($action) {
                    Assert::assertFalse($isMatchForAction, sprintf('A form exists with action "%s"', $action));
                } else {
                    Assert::assertFalse($isMatchForMethod, sprintf('A form exists with method "%s"', $method));
                }
            }

            return $this;
        };
    }
}
