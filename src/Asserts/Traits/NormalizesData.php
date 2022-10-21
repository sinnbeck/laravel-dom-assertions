<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

use Illuminate\Support\Str;

trait NormalizesData
{
    protected function normalizeAttributesArray(array $attributes): array
    {
        foreach ($attributes as $attribute => $value) {
            $attributes[$attribute] = $this->normalizeAttributeValue($attribute, $value);
        }

        return $attributes;
    }

    protected function normalizeAttributeValue($attribute, $value): mixed
    {
        if ($attribute === 'class') {
            return $this->normalizeClass($value);
        }

        if (in_array($attribute, ['readonly', 'required']) && ! $value) {
            return true;
        }

        return $value;
    }

    protected function normalizeClass(string $class): string
    {
        return Str::of($class)
            ->explode(' ')
            ->sort()
            ->implode(' ');
    }
}
