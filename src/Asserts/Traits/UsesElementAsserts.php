<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

use Illuminate\Testing\Assert as PHPUnit;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Asserts\ElementAssert;
use Sinnbeck\DomAssertions\Formatters\Normalize;

trait UsesElementAsserts
{
    public function has(string $attribute, mixed $value = null): self
    {
        if (!$value) {
            PHPUnit::assertTrue(
                $this->hasAttribute($attribute),
                sprintf('Could not find an attribute "%s"', $attribute)
            );

            return $this;
        }

        if ($attribute === 'class') {
            PHPUnit::assertTrue(
                ! array_diff(
                    Normalize::attributeValue($attribute, $value),
                    $this->getAttribute($attribute)
                ),
                sprintf('Could not find an attribute "%s" with value "%s"', $attribute, $value)
            );

            return $this;
        }

        PHPUnit::assertEquals(
            Normalize::attributeValue($attribute, $value),
            $this->getAttribute($attribute),
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

        if (!is_null($callback)) {
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

        if (!$attributes && !$count) {
            return $this;
        }

        if (!$attributes) {
            Assert::assertEquals(
                $count,
                $found = $this->getParser()->queryAll($elementName)->count(),
                sprintf('Expected to find %s elements but found %s for %s', $count, $found, $elementName)
            );

            return $this;
        }

        $this->gatherAttributes($elementName);
        $normalizedAttributes = Normalize::attributesArray($attributes);

        if ($count) {
            $found = collect($this->attributes[$elementName])
                ->filter(fn($foundAttributes) => $this->compareAttributesArrays($normalizedAttributes, $foundAttributes))
                ->count();

            Assert::assertEquals(
                $count,
                $found,
                sprintf('Expected to find %s elements but found %s for %s', $count, $found, $elementName)
            );
        }

        $first = collect($this->attributes[$elementName])
            ->search(fn($attribute) => $this->compareAttributesArrays($normalizedAttributes, $attribute));

        Assert::assertNotFalse(
            $first,
            sprintf('Could not find a matching "%s" with data: %s', $elementName, json_encode($attributes, JSON_PRETTY_PRINT))
        );

        return $this;
    }

    public function doesntContain(string $elementName, array $attributes = []): self
    {
        if (!$attributes) {
            Assert::assertNull(
                $this->getParser()->query($elementName),
                sprintf('Found a matching element of type "%s"', $elementName)
            );

            return $this;
        }

        $this->gatherAttributes($elementName);

        $first = collect($this->attributes[$elementName])
            ->search(fn($foundAttributes) => $this->compareAttributesArrays(Normalize::attributesArray($attributes), $foundAttributes));

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

            if ($attribute === 'class') {
                return ! array_diff($value, $foundAttributes[$attribute]);
            }

             if ($value != $foundAttributes[$attribute]) {
                 return false;
             }
        }
        return true;
        return !array_diff_assoc($attributes, $foundAttributes);
    }
}
