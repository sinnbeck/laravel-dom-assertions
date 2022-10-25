<?php

namespace Sinnbeck\DomAssertions\Support;

use Sinnbeck\DomAssertions\Support\Matchers\Classes;
use Sinnbeck\DomAssertions\Support\Matchers\NoValues;
use Sinnbeck\DomAssertions\Support\Matchers\Text;
use Sinnbeck\DomAssertions\Support\Matchers\Values;

/**
 * @internal
 */
class CompareAttributes
{
    public static function compare($attribute, $value, $actual): bool
    {
        if (! $value) {
            return NoValues::compare($value, $actual);
        }

        return match ($attribute) {
            'class' => Classes::compare($value, $actual),
            'required', 'readonly' => NoValues::compare($value, $actual),
            'text', 'value' => Text::compare($value, $actual),
            default => Values::compare($value, $actual),
        };
    }
}
