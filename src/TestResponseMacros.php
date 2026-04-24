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
use Sinnbeck\DomAssertions\Support\Normalize;

/**
 * @internal
 *
 * @mixin TestResponse
 */
class TestResponseMacros
{
    /**
     * @internal
     */
    protected function getDomParser(): Closure
    {
        return function (): DomParser {
            /** @var TestResponse $this */
            $content = $this->getContent();

            // Due to being the test response, livewire users can access the DOM assertions.
            // If the component is updated and the content-type is json, we attempt to render the html.
            if ($this->headers->get('content-type') === 'application/json') {
                $json = json_decode($content, true);
                if (isset($json['components'][0]['effects']['html'])) {
                    $content = $json['components'][0]['effects']['html'];
                }
            }

            $hash = hash('xxh128', $content);

            $cacheKey = 'dom-assertions.parser.'.$hash;

            if (! app()->has($cacheKey)) {
                try {
                    app()->instance($cacheKey, DomParser::new($content));
                } catch (DOMException $exception) {
                    Assert::fail($exception->getMessage());
                }
            }

            return app()->make($cacheKey);
        };
    }

    public function assertHtml5(): Closure
    {
        return function (): TestResponse {
            /** @var TestResponse $this */
            Assert::assertNotEmpty(
                $this->getContent(),
                'The view is empty!'
            );

            $parser = $this->getDomParser();

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

            $parser = $this->getDomParser();

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

            $parser = $this->getDomParser();

            Assert::assertNotNull(
                $parser->query($selector),
                sprintf('No element found with selector: %s', $selector)
            );

            if (empty($attributes)) {
                return $this;
            }

            $elements = $parser->queryAll($selector);

            foreach ($attributes as $attribute => $expected) {
                $matched = false;

                foreach ($elements as $element) {
                    if (! $element instanceof DOMElement) {
                        continue;
                    }

                    if ($attribute === 'text') {
                        $actual = Normalize::text($element->textContent);
                        if (str_contains($actual, (string) $expected)) {
                            $matched = true;
                            break;
                        }
                    } else {
                        $actual = $element->getAttribute($attribute);
                        if ($actual !== '' && str_contains($actual, (string) $expected)) {
                            $matched = true;
                            break;
                        }
                    }
                }

                if (! $matched) {
                    $attribute === 'text'
                        ? Assert::fail(sprintf('Failed asserting that any element [%s] text contains "%s".', $selector, $expected))
                        : Assert::fail(sprintf('Failed asserting that attribute [%s] of any element [%s] contains "%s".', $attribute, $selector, $expected));
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

            $parser = $this->getDomParser();

            $element = $parser->query($selector);

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

            $parser = $this->getDomParser();

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

            $parser = $this->getDomParser();

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

    public function ddContent(): Closure
    {
        return function (): void {
            /** @var TestResponse $this */
            dd($this->getContent());
        };
    }
}
