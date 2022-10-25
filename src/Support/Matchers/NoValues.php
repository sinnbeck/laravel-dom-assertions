<?php

namespace Sinnbeck\DomAssertions\Support\Matchers;

/**
 * @internal
 */
class NoValues implements Matcher
{
    public static function compare($expected, $actual): bool
    {
        return true;
    }
}
