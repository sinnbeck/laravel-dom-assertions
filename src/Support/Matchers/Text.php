<?php

namespace Sinnbeck\DomAssertions\Support\Matchers;

use Sinnbeck\DomAssertions\Support\Normalize;

/**
 * @internal
 */
class Text implements Matcher
{
    public static function compare($expected, $actual): bool
    {
        return Normalize::text($expected) === Normalize::text($actual);
    }
}
