<?php

namespace Sinnbeck\DomAssertions\Utilities;

use Sinnbeck\DomAssertions\Utilities\Matchers\Classes;
use Sinnbeck\DomAssertions\Utilities\Matchers\NoValues;
use Sinnbeck\DomAssertions\Utilities\Matchers\Text;
use Sinnbeck\DomAssertions\Utilities\Matchers\Values;

class CompareAttributes
{
    public static function compare($attribute, $value, $actual)
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
