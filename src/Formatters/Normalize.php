<?php

namespace Sinnbeck\DomAssertions\Formatters;

use Illuminate\Support\Str;

class Normalize
{
    public static function attributesArray(array $attributes): array
    {
        foreach ($attributes as $attribute => $value) {
            $attributes[$attribute] = self::attributeValue($attribute, $value);
        }

        return $attributes;
    }

    public static function attributeValue($attribute, $value): mixed
    {
        if ($attribute === 'class') {
            return Normalize::className($value);
        }

        if ($attribute === 'text') {
            return trim($value);
        }

        if (in_array($attribute, [
            'readonly',
            'required',
        ]) && ! $value) {
            return true;
        }

        return $value;
    }

    protected static function className(string $class): array
    {
        return Str::of($class)
            ->explode(' ')
            ->sort()
            ->values()
            ->toArray();
    }
}
