<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions;

use Closure;
use DOMElement;
use DOMException;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use Sinnbeck\DomAssertions\Asserts\AssertForm;
use Sinnbeck\DomAssertions\Asserts\AssertSelect;
use Sinnbeck\DomAssertions\Support\DomParser;

/**
 * @internal
 *
 * @mixin TestResponse
 */
class TestResponseMacros
{
    public function assertHtml5(): Closure
    {
        return function () {
            /** @var TestResponse $this */
            Assert::assertNotEmpty(
                $this->getContent(),
                'The view is empty!'
            );

            try {
                $parser = DomParser::new($this->getContent());
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

    public function assertElement(): Closure
    {
        return $this->assertElementExists();
    }

    public function assertElementExists(): Closure
    {
        return function ($selector = 'body', $callback = null): TestResponse {
            /** @var TestResponse $this */
            Assert::assertNotEmpty(
                $this->getContent(),
                'The view is empty!'
            );

            try {
                $parser = DomParser::new($this->getContent());
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
                $callback(new AssertElement($this->getContent(), $element));
            }

            return $this;
        };
    }

    public function assertContainsElement(): Closure
    {
        return function (string $selector, array $attributes = []): TestResponse {
            /** @var TestResponse $this */
            Assert::assertNotEmpty(
                (string) $this->getContent(),
                'The response is empty!'
            );

            try {
                if (! app()->has('dom-assertions.parser')) {
                    app()->instance('dom-assertions.parser', DomParser::new($this->getContent()));
                }
            } catch (DOMException $exception) {
                Assert::fail($exception->getMessage());
            }

            $element = app()->make('dom-assertions.parser')->query($selector);

            Assert::assertNotNull(
                $element,
                sprintf('No element found with selector: %s', $selector)
            );

            if (! $element instanceof DOMElement) {
                Assert::fail('The element found is not a DOMElement!');
            }

            foreach ($attributes as $attribute => $expected) {
                switch ($attribute) {
                    case 'text':
                        $actual = trim($element->textContent);
                        Assert::assertStringContainsString(
                            $expected,
                            $actual,
                            sprintf(
                                'Failed asserting that element [%s] text contains "%s". Actual: "%s".',
                                $selector,
                                $expected,
                                $actual
                            )
                        );
                        break;

                    default:
                        $actual = $element->getAttribute($attribute);
                        Assert::assertNotEmpty(
                            $actual,
                            sprintf('Attribute [%s] not found in element [%s].', $attribute, $selector)
                        );

                        Assert::assertStringContainsString(
                            $expected,
                            $actual,
                            sprintf(
                                'Failed asserting that attribute [%s] of element [%s] contains "%s". Actual: "%s".',
                                $attribute,
                                $selector,
                                $expected,
                                $actual
                            )
                        );
                        break;
                }
            }

            return $this;
        };
    }

    public function assertDoesntExist(): Closure
    {
        return function (string $selector): TestResponse {
            /** @var TestResponse $this */
            Assert::assertNotEmpty(
                (string) $this->getContent(),
                'The view is empty!'
            );

            try {
                if (! app()->has('dom-assertions.parser')) {
                    app()->instance('dom-assertions.parser', DomParser::new($this->getContent()));
                }
            } catch (DOMException $exception) {
                Assert::fail($exception->getMessage());
            }

            $element = app()->make('dom-assertions.parser')->query($selector);

            Assert::assertNull(
                $element,
                sprintf('Expected no element with selector: %s, but one was found.', $selector)
            );

            return $this;
        };
    }

    public function assertForm(): Closure
    {
        return $this->assertFormExists();
    }

    public function assertFormExists(): Closure
    {
        return function ($selector = 'form', $callback = null): TestResponse {
            /** @var TestResponse $this */
            Assert::assertNotEmpty(
                $this->getContent(),
                'The view is empty!'
            );

            try {
                $parser = DomParser::new($this->getContent());
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
                $callback(new AssertForm($this->getContent(), $form));
            }

            return $this;
        };
    }

    public function assertSelect(): Closure
    {
        return $this->assertSelectExists();
    }

    public function assertSelectExists(): Closure
    {
        return function ($selector = 'select', $callback = null): TestResponse {
            /** @var TestResponse $this */
            Assert::assertNotEmpty(
                $this->getContent(),
                'The view is empty!'
            );

            try {
                $parser = DomParser::new($this->getContent());
            } catch (DOMException $exception) {
                Assert::fail($exception->getMessage());
            }

            if ($selector instanceof Closure) {
                $callback = $selector;
                $selector = 'select';
            }

            if (is_string($selector)) {
                $select = $parser->query($selector);
            } else {
                Assert::fail('Invalid selector!');
            }

            Assert::assertNotNull(
                $select,
                sprintf('No select was found with selector "%s"', $selector)
            );
            Assert::assertEquals(
                'select',
                $select->nodeName,
                'Element is not of type select!');

            if ($callback) {
                $callback(new AssertSelect($this->getContent(), $select));
            }

            return $this;
        };
    }
}
