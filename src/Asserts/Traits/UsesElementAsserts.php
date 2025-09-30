<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

use Illuminate\Testing\Assert as PHPUnit;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Asserts\AssertElement;
use Sinnbeck\DomAssertions\Support\CompareAttributes;

/**
 * @internal
 */
trait UsesElementAsserts
{
    public function has(string $attribute, mixed $value = null): self
    {
        if (! $value) {
            PHPUnit::assertTrue(
                $this->hasAttribute($attribute),
                sprintf('Could not find an attribute "%s"', $attribute)
            );

            return $this;
        }

        PHPUnit::assertTrue(
            CompareAttributes::compare(
                $attribute,
                $value,
                $found = $this->getAttribute($attribute)
            ),
            sprintf('Could not find an attribute "%s" with value "%s". "%s" found within: %s', $attribute, $value, trim($found), $this->getSelectors())
        );

        return $this;
    }

    public function doesntHave(string $attribute, mixed $value = null): self
    {
        if (! $value) {
            PHPUnit::assertFalse(
                $this->hasAttribute($attribute),
                sprintf('Found an attribute "%s"', $attribute)
            );

            return $this;
        }

        PHPUnit::assertFalse(
            CompareAttributes::compare(
                $attribute,
                $value,
                $this->getAttribute($attribute)
            ),
            sprintf('Found an attribute "%s" with value "%s" within: %s', $attribute, $value, $this->getSelectors())
        );

        return $this;
    }

    public function find(string $selector, $callback = null): self
    {
        Assert::assertNotNull(
            $element = $this->getParser()->query($selector),
            sprintf('Could not find any matching element for selector "%s"', $selector)
        );

        if (! is_null($callback)) {
            $elementAssert = new AssertElement($this->getContent(), $element);

            $elementAssert->withSelectors($this->selectorPath, $selector);

            $callback($elementAssert);
        }

        return $this;
    }

    public function each(string $selector, $callback): self
    {
        $elements = $this->getParser()->queryAll($selector);
        Assert::assertNotEmpty(
            $elements,
            sprintf('Could not find any matching element for selector "%s"', $selector)
        );

        foreach ($elements as $index => $element) {
            $elementAssert = new AssertElement($this->getContent(), $element);
            $callback($elementAssert, $index);
        }

        return $this;
    }

    public function contains(string $selector, $attributes = null, $count = 0): self
    {
        Assert::assertNotNull(
            $this->getParser()->query($selector),
            sprintf('Could not find any matching element of type "%s" within: %s', $selector, $this->getSelectors())
        );

        if (is_numeric($attributes)) {
            $count = $attributes;
            $attributes = null;
        }

        if (! $attributes && ! $count) {
            return $this;
        }

        if (! $attributes) {
            Assert::assertEquals(
                $count,
                $found = $this->getParser()->queryAll($selector)->count(),
                sprintf('Expected to find %s elements but found %s for %s within: %s', $count, $found, $selector, $this->getSelectors())
            );

            return $this;
        }

        $this->gatherAttributes($selector);

        if ($count) {
            Assert::assertEquals(
                $count,
                $found = collect($this->attributes[$selector])
                    ->filter(fn ($foundAttributes) => $this->compareAttributesArrays($attributes, $foundAttributes))
                    ->count(),
                sprintf('Expected to find %s elements but found %s for %s within: %s', $count, $found, $selector, $this->getSelectors())
            );
        }

        $first = collect($this->attributes[$selector])
            ->search(fn ($attribute) => $this->compareAttributesArrays($attributes, $attribute));

        Assert::assertNotFalse(
            $first,
            sprintf('Could not find a matching "%s" with data: %s within: %s', $selector, json_encode($attributes, JSON_PRETTY_PRINT), $this->getSelectors())
        );

        return $this;
    }

    public function doesntContain(string $elementName, array $attributes = []): self
    {
        if (! $attributes) {
            $found = $this->getParser()->query($elementName);
            Assert::assertNull(
                $found ? get_class($found) : null,
                sprintf('Found a matching element of type "%s"', $elementName)
            );

            return $this;
        }

        $this->gatherAttributes($elementName);

        $first = collect($this->attributes[$elementName])
            ->search(fn ($foundAttributes) => $this->compareAttributesArrays($attributes, $foundAttributes));

        Assert::assertFalse(
            $first,
            sprintf('Found a matching "%s" with data: %s within: %s', $elementName, json_encode($attributes, JSON_PRETTY_PRINT), $this->getSelectors())
        );

        return $this;
    }

    public function containsText(string $needle, bool $ignoreCase = false): self
    {
        $text = $this->getAttribute('text');

        $assertFunction = $ignoreCase ?
            'assertStringContainsStringIgnoringCase' :
            'assertStringContainsString';

        call_user_func(
            [PHPUnit::class, $assertFunction],
            $needle,
            $text,
            sprintf('Could not find text content "%s" containing %s within: %s', $text, $needle, $this->getSelectors())
        );

        return $this;
    }

    public function doesntContainText(string $needle, bool $ignoreCase = false): self
    {
        $text = $this->getAttribute('text');

        $assertFunction = $ignoreCase ?
            'assertStringNotContainsStringIgnoringCase' :
            'assertStringNotContainsString';

        call_user_func(
            [PHPUnit::class, $assertFunction],
            $needle,
            $text,
            sprintf('Found text content "%s" containing %s within: %s', $text, $needle, $this->getSelectors())
        );

        return $this;
    }

    public function is(string $type): self
    {
        PHPUnit::assertEquals(
            $type,
            $this->getParser()->getType(),
            sprintf('Element is not of type "%s" within: %s', $type, $this->getSelectors())
        );

        return $this;
    }

    private function compareAttributesArrays($attributes, $foundAttributes): bool
    {
        foreach ($attributes as $attribute => $value) {
            if (! isset($foundAttributes[$attribute])) {
                return false;
            }

            if (! CompareAttributes::compare($attribute, $value, $foundAttributes[$attribute])) {
                return false;
            }
        }

        return true;
    }

    abstract protected function getParser();
}
