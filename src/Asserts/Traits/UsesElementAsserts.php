<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

use Illuminate\Testing\Assert as PHPUnit;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Asserts\ElementAssert;
use Sinnbeck\DomAssertions\Utilities\CompareAttributes;

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
                $this->getAttribute($attribute)
            ),
            sprintf('Could not find an attribute "%s" with value "%s"', $attribute, $value)
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
            $elementAssert = new ElementAssert($this->getContent(), $element);
            $callback($elementAssert);
        }

        return $this;
    }

    public function contains(string $elementName, $attributes = null, $count = 0): self
    {
        Assert::assertNotNull(
            $this->getParser()->query($elementName),
            sprintf('Could not find any matching element of type "%s"', $elementName)
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
                $found = $this->getParser()->queryAll($elementName)->count(),
                sprintf('Expected to find %s elements but found %s for %s', $count, $found, $elementName)
            );

            return $this;
        }

        $this->gatherAttributes($elementName);

        if ($count) {
            Assert::assertEquals(
                $count,
                $found = collect($this->attributes[$elementName])
                    ->filter(fn ($foundAttributes) => $this->compareAttributesArrays($attributes, $foundAttributes))
                    ->count(),
                sprintf('Expected to find %s elements but found %s for %s', $count, $found, $elementName)
            );
        }

        $first = collect($this->attributes[$elementName])
            ->search(fn ($attribute) => $this->compareAttributesArrays($attributes, $attribute));

        Assert::assertNotFalse(
            $first,
            sprintf('Could not find a matching "%s" with data: %s', $elementName, json_encode($attributes, JSON_PRETTY_PRINT))
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
            sprintf('Found a matching "%s" with data: %s', $elementName, json_encode($attributes, JSON_PRETTY_PRINT))
        );

        return $this;
    }

    public function is(string $type): self
    {
        PHPUnit::assertEquals(
            $type,
            $this->getParser()->getType(),
            sprintf('Element is not of type "%s"', $type)
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
