<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions;

use Closure;
use DOMElement;
use DOMException;
use Illuminate\Testing\TestView;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use Sinnbeck\DomAssertions\Asserts\AssertForm;
use Sinnbeck\DomAssertions\Asserts\AssertSelect;
use Sinnbeck\DomAssertions\Support\DomParser;
use Sinnbeck\DomAssertions\Support\Normalize;

/**
 * @internal
 *
 * @mixin TestView
 */
class TestViewMacros
{
    /**
     * @internal
     */
    protected function getDomParser(): Closure
    {
        return function (): DomParser {
            /** @var TestView $this */
            $content = (string) $this;

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
        return function (): TestView {
            /** @var TestView $this */
            Assert::assertNotEmpty(
                (string) $this,
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
        return function ($selector = 'body', $callback = null): TestView {
            /** @var TestView $this */
            Assert::assertNotEmpty(
                (string) $this,
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
                $callback(new AssertElement((string) $this, $element));
            }

            return $this;
        };
    }

    public function assertContainsElement(): Closure
    {
        return function (string $selector, array $attributes = []): TestView {
            /** @var TestView $this */
            Assert::assertNotEmpty(
                (string) $this,
                'The view is empty!'
            );

            $parser = $this->getDomParser();

            Assert::assertNotNull(
                $parser->query($selector),
                sprintf('No element found with selector: %s', $selector)
            );

            if ($attributes === []) {
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
        return function (string $selector): TestView {
            /** @var TestView $this */
            Assert::assertNotEmpty(
                (string) $this,
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
        return function ($selector = 'form', $callback = null): TestView {
            /** @var TestView $this */
            Assert::assertNotEmpty(
                (string) $this,
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
                $callback(new AssertForm((string) $this, $form));
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
        return function ($selector = 'select', $callback = null): TestView {
            /** @var TestView $this */
            Assert::assertNotEmpty(
                (string) $this,
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
                $callback(new AssertSelect((string) $this, $select));
            }

            return $this;
        };
    }

    public function ddContent(): Closure
    {
        return function (): void {
            /** @var TestView $this */
            dd((string) $this);
        };
    }
}
