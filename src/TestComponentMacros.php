<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions;

use Closure;
use DOMException;
use Illuminate\Testing\TestComponent;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use Sinnbeck\DomAssertions\Asserts\AssertForm;
use Sinnbeck\DomAssertions\Support\DomParser;

/**
 * @internal
 *
 * @mixin TestComponent
 */
class TestComponentMacros
{
    public function assertHtml5()
    {
        return function () {
            /** @var TestComponent $this */
            Assert::assertNotEmpty(
                (string) $this->rendered,
                'The component is empty!'
            );

            try {
                $parser = DomParser::new((string) $this->rendered);
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
        return function ($selector = 'div', $callback = null): TestComponent {
            /** @var TestComponent $this */
            Assert::assertNotEmpty(
                (string) $this->rendered,
                'The component is empty!'
            );

            try {
                $parser = DomParser::new((string) $this->rendered);
            } catch (DOMException $exception) {
                Assert::fail($exception->getMessage());
            }

            if ($selector instanceof Closure) {
                $callback = $selector;
                $selector = 'div';
            }

            if (is_string($selector)) {
                $element = $parser->query($selector);
            } else {
                Assert::fail('Invalid selector!');
            }

            Assert::assertNotNull($element, sprintf('No element found with selector: %s', $selector));

            if ($callback) {
                $callback(new AssertElement((string) $this->rendered, $element));
            }

            return $this;
        };
    }

    public function assertFormExists(): Closure
    {
        return function ($selector = 'form', $callback = null): TestComponent {
            /** @var TestComponent $this */
            Assert::assertNotEmpty(
                (string) $this->rendered,
                'The component is empty!'
            );

            try {
                $parser = DomParser::new((string) $this->rendered);
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
                $callback(new AssertForm((string) $this->rendered, $form));
            }

            return $this;
        };
    }
}
