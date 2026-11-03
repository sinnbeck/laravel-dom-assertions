<?php

namespace Sinnbeck\DomAssertions;

use Closure;
use DOMElement;
use DOMException;
use Illuminate\Testing\TestComponent;
use Illuminate\Testing\TestResponse;
use Illuminate\Testing\TestView;
use InvalidArgumentException;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use Sinnbeck\DomAssertions\Asserts\AssertForm;
use Sinnbeck\DomAssertions\Asserts\AssertSelect;
use Sinnbeck\DomAssertions\Support\DomParser;
use Sinnbeck\DomAssertions\Support\Html;
use Sinnbeck\DomAssertions\Support\MacroTarget;
use Sinnbeck\DomAssertions\Support\Normalize;

abstract class DomAssertionMacros
{
    /**
     * @return Closure(): string
     */
    abstract public function content(): Closure;

    abstract public function emptyMessage(): string;

    /**
     * @internal
     *
     * @return Closure(string): DomParser
     */
    protected function getDomParser(): Closure
    {
        return function (string $html): DomParser {
            $hash = hash('xxh128', $html);

            $cacheKey = 'dom-assertions.parser.'.$hash;

            if (! app()->has($cacheKey)) {
                try {
                    app()->instance($cacheKey, DomParser::new($html));
                } catch (DOMException $exception) {
                    Assert::fail($exception->getMessage());
                }
            }

            return app()->make($cacheKey);
        };
    }

    /**
     * @return Closure(): (TestComponent|TestHtml|TestResponse|TestView)
     */
    public function assertHtml5(): Closure
    {
        $emptyMessage = $this->emptyMessage();
        $content = $this->content();
        $domParser = $this->getDomParser();

        return function () use ($emptyMessage, $content, $domParser): TestComponent|TestHtml|TestResponse|TestView {
            $html = $content->call($this);

            Assert::assertNotEmpty(
                $html,
                $emptyMessage
            );

            $parser = $domParser($html);

            Assert::assertEquals(
                'html',
                $parser->getDocType(),
                'Not a html5 doctype!'
            );

            return MacroTarget::from($this);
        };
    }

    /**
     * @return Closure(string|Closure(AssertElement): void=, ?callable(AssertElement): void=): (TestComponent|TestHtml|TestResponse|TestView)
     */
    public function assertElement(): Closure
    {
        return $this->assertElementExists();
    }

    /**
     * @return Closure(string|Closure(AssertElement): void=, ?callable(AssertElement): void=): (TestComponent|TestHtml|TestResponse|TestView)
     */
    public function assertElementExists(): Closure
    {
        $emptyMessage = $this->emptyMessage();
        $content = $this->content();
        $domParser = $this->getDomParser();

        return function ($selector = 'body', $callback = null) use ($emptyMessage, $content, $domParser): TestComponent|TestHtml|TestResponse|TestView {
            $html = $content->call($this);

            Assert::assertNotEmpty(
                $html,
                $emptyMessage
            );

            $parser = $domParser($html);

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
                $callback(new AssertElement($html, $element));
            }

            return MacroTarget::from($this);
        };
    }

    /**
     * @return Closure(string, string, bool=, ?bool=): (TestComponent|TestHtml|TestResponse|TestView)
     */
    public function assertElementContainsText(): Closure
    {
        $elementExists = $this->assertElementExists();

        return function (string $selector, string $needle, bool $ignoreCase = false, ?bool $normalizeWhitespace = null) use ($elementExists): TestComponent|TestHtml|TestResponse|TestView {
            return $elementExists->call($this, $selector, static function (AssertElement $assert) use ($needle, $ignoreCase, $normalizeWhitespace): void {
                $assert->containsText($needle, $ignoreCase, $normalizeWhitespace);
            });
        };
    }

    /**
     * @return Closure(string, string, bool=): (TestComponent|TestHtml|TestResponse|TestView)
     */
    public function assertElementContainsNormalizedText(): Closure
    {
        $elementContainsText = $this->assertElementContainsText();

        return function (string $selector, string $needle, bool $ignoreCase = false) use ($elementContainsText): TestComponent|TestHtml|TestResponse|TestView {
            return $elementContainsText->call($this, $selector, $needle, $ignoreCase, true);
        };
    }

    /**
     * @return Closure(string, array<string, mixed>=): (TestComponent|TestHtml|TestResponse|TestView)
     */
    public function assertContainsElement(): Closure
    {
        $emptyMessage = $this->emptyMessage();
        $content = $this->content();
        $domParser = $this->getDomParser();

        return function (string $selector, array $attributes = []) use ($emptyMessage, $content, $domParser): TestComponent|TestHtml|TestResponse|TestView {
            $html = $content->call($this);

            Assert::assertNotEmpty(
                $html,
                $emptyMessage
            );

            $parser = $domParser($html);

            Assert::assertNotNull(
                $parser->query($selector),
                sprintf('No element found with selector: %s', $selector)
            );

            if ($attributes === []) {
                return MacroTarget::from($this);
            }

            $elements = $parser->queryAll($selector);

            foreach ($attributes as $attribute => $expected) {
                $matched = false;

                foreach ($elements as $element) {
                    if (! $element instanceof DOMElement) {
                        continue;
                    }

                    if ($attribute === 'text') {
                        $needle = (string) $expected;
                        $rawText = str_replace("\r\n", "\n", $element->textContent);
                        $normalizedText = Normalize::text($rawText);

                        if (str_contains($normalizedText, $needle) || str_contains($rawText, $needle)) {
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

            return MacroTarget::from($this);
        };
    }

    /**
     * @return Closure(string): (TestComponent|TestHtml|TestResponse|TestView)
     */
    public function assertDoesntExist(): Closure
    {
        $emptyMessage = $this->emptyMessage();
        $content = $this->content();
        $domParser = $this->getDomParser();

        return function (string $selector) use ($emptyMessage, $content, $domParser): TestComponent|TestHtml|TestResponse|TestView {
            $html = $content->call($this);

            Assert::assertNotEmpty(
                $html,
                $emptyMessage
            );

            $parser = $domParser($html);

            $element = $parser->query($selector);

            Assert::assertNull(
                $element,
                sprintf('Expected no element with selector: %s, but one was found.', $selector)
            );

            return MacroTarget::from($this);
        };
    }

    /**
     * @return Closure(string|Closure(AssertForm): void=, ?callable(AssertForm): void=): (TestComponent|TestHtml|TestResponse|TestView)
     */
    public function assertForm(): Closure
    {
        return $this->assertFormExists();
    }

    /**
     * @return Closure(string|Closure(AssertForm): void=, ?callable(AssertForm): void=): (TestComponent|TestHtml|TestResponse|TestView)
     */
    public function assertFormExists(): Closure
    {
        $emptyMessage = $this->emptyMessage();
        $content = $this->content();
        $domParser = $this->getDomParser();

        return function ($selector = 'form', $callback = null) use ($emptyMessage, $content, $domParser): TestComponent|TestHtml|TestResponse|TestView {
            $html = $content->call($this);

            Assert::assertNotEmpty(
                $html,
                $emptyMessage
            );

            $parser = $domParser($html);

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
                $callback(new AssertForm($html, $form));
            }

            return MacroTarget::from($this);
        };
    }

    /**
     * @return Closure(string|Closure(AssertSelect): void=, ?callable(AssertSelect): void=): (TestComponent|TestHtml|TestResponse|TestView)
     */
    public function assertSelect(): Closure
    {
        return $this->assertSelectExists();
    }

    /**
     * @return Closure(string|Closure(AssertSelect): void=, ?callable(AssertSelect): void=): (TestComponent|TestHtml|TestResponse|TestView)
     */
    public function assertSelectExists(): Closure
    {
        $emptyMessage = $this->emptyMessage();
        $content = $this->content();
        $domParser = $this->getDomParser();

        return function ($selector = 'select', $callback = null) use ($emptyMessage, $content, $domParser): TestComponent|TestHtml|TestResponse|TestView {
            $html = $content->call($this);

            Assert::assertNotEmpty(
                $html,
                $emptyMessage
            );

            $parser = $domParser($html);

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
                $callback(new AssertSelect($html, $select));
            }

            return MacroTarget::from($this);
        };
    }

    /**
     * @return Closure(string, array<array-key, mixed>=): TestHtml
     */
    public function wrap(): Closure
    {
        $emptyMessage = $this->emptyMessage();
        $content = $this->content();

        return function (string $element, array $attributes = []) use ($emptyMessage, $content): TestHtml {
            $html = $content->call($this);

            Assert::assertNotEmpty(
                $html,
                $emptyMessage
            );

            try {
                $wrapped = Html::element($element, $html, $attributes);
            } catch (InvalidArgumentException $exception) {
                Assert::fail($exception->getMessage());
            }

            return TestHtml::make($wrapped);
        };
    }

    /**
     * @return Closure(): void
     */
    public function ddContent(): Closure
    {
        $content = $this->content();

        return function () use ($content): void {
            dd($content->call($this));
        };
    }
}
