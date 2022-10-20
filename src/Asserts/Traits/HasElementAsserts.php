<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

use Illuminate\Support\Str;
use Illuminate\Testing\Assert as PHPUnit;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Asserts\ElementAssert;

trait HasElementAsserts
{
    public function __call(string $method, array $arguments)
    {
        if (Str::startsWith($method, 'has')) {
            $property = Str::of($method)->after('has')->snake()->slug('-');
            $this->has($property, $arguments[0]);
        }

        if (Str::startsWith($method, 'is')) {
            $property = Str::of($method)->after('is')->snake()->slug('-');
            $this->is($property);
        }

        if (Str::startsWith($method, 'find')) {
            $property = Str::of($method)->after('find')->snake()->slug('-');
            $this->find($property, $arguments[0]);
        }

        if (Str::startsWith($method, 'contains')) {
            $elementName = Str::of($method)->after('contains')->camel();
            $this->contains($elementName, ...$arguments);
        }

        if (Str::startsWith($method, 'doesntContain')) {
            $elementName = Str::of($method)->after('doesntContain')->camel();
            $this->doesntContain($elementName, ...$arguments);
        }

        return $this;
    }

    public function has(string $attribute, mixed $value): self
    {
        PHPUnit::assertEquals(
            $value,
            $this->getAttribute($attribute),
            sprintf('Could not find an attribute "%s" with value "%s"', $attribute, $value)
        );

        return $this;
    }

    public function find(string $selector, $callback = null): self
    {
        Assert::assertNotNull(
            $element = $this->parser->query($selector),
            sprintf('Could not find any matching element for selector "%s"', $selector)
        );

        if (! is_null($callback)) {
            $elementAssert = new ElementAssert($this->getContent(), $element);
            $callback($elementAssert);
        }

        return $this;
    }

    public function contains(string $elementName, array $attributes = []): self
    {
        Assert::assertNotNull(
            $this->parser->query($elementName),
            sprintf('Could not find any matching element of type "%s"', $elementName)
        );

        if (! $attributes) {
            return $this;
        }

        $this->gatherAttributes($elementName);

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
            Assert::assertNull(
                $this->parser->query($elementName),
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
            $this->parser->getType(),
            sprintf('Element is not of type "%s"', $type)
        );

        return $this;
    }

    private function compareAttributesArrays($attributes, $foundAttributes)
    {
        return ! array_diff($attributes, $foundAttributes);
    }
}
