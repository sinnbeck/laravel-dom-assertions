<?php

namespace Sinnbeck\DomAssertions\Utilities\Matchers;

use Sinnbeck\DomAssertions\Utilities\Normalize;

class Classes implements Matcher
{
    public static function compare($expected, $actual)
    {
        return ! array_diff(Normalize::class($expected), Normalize::class($actual));
    }
}
