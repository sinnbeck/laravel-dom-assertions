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
use Sinnbeck\DomAssertions\Support\DomParser;

/**
 * @internal
 *
 * @mixin TestView
 */
class TestViewMacros
{
    public function assertHtml5(): Closure
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

    public function assertContainsElement(): Closure
    {
        return function (string $selector, array $attributes = []): TestView {
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

            $element = $parser->query($selector);

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
                        Assert::assertEquals(
                            $expected,
                            $actual,
                            sprintf(
                                'Failed asserting that element [%s] text is "%s". Actual: "%s".',
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
}
