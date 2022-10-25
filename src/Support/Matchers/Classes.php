<?php

namespace Sinnbeck\DomAssertions\Support\Matchers;

use Sinnbeck\DomAssertions\Support\Normalize;

/**
 * @internal
 */
class Classes implements Matcher
{
    public static function compare($expected, $actual): bool
    {
        return ! array_diff(Normalize::class($expected), Normalize::class($actual));
    }
}
