<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

use Illuminate\Support\Str;
use Illuminate\Testing\Assert as PHPUnit;
use PHPUnit\Framework\Assert;

trait HasElementAsserts
{
    public function __call(string $method, array $arguments)
    {
        if (Str::startsWith($method, 'contains')) {
            $elementName = Str::of($method)->after('contains')->camel();
            $this->contains($elementName, ...$arguments);
        }

        if (Str::startsWith($method, 'has')) {
            $property = Str::of($method)->after('has')->snake()->slug('-');
            $this->has($property, $arguments[0]);
        }

        return $this;
    }

    public function has(string $attribute, mixed $value): self
    {
        PHPUnit::assertEquals(
            $this->getAttribute($attribute),
            $value,
            sprintf('Could not find an attribute %s with value %s', $attribute, $value)
        );

        return $this;
    }

    public function contains(string $elementName, array $attributes): self
    {
        $this->gatherAttributes($elementName);

        $first = collect($this->attributes[$elementName])
            ->search(fn ($input) => array_intersect_key($attributes, $input) === $attributes);

        Assert::assertNotFalse(
            $first,
            sprintf('Could not find a matching textarea with data: %s', json_encode($attributes, JSON_PRETTY_PRINT))
        );

        return $this;
    }

    protected function getAttribute(string $attribute)
    {
        return $this->parser->getAttributeForRoot($attribute);
    }

    protected function getAttributeFor(string $for, string $attribute)
    {
        return $this->parser->getAttributeFor($for, $attribute);
    }
}
