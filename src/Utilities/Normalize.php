<?php

namespace Sinnbeck\DomAssertions\Utilities;

use Illuminate\Support\Str;

class Normalize
{
    public static function class(string $class): array
    {
        return Str::of($class)
            ->explode(' ')
            ->sort()
            ->values()
            ->toArray();
    }

    public static function text(string $text)
    {
        return trim($text);
    }
}
