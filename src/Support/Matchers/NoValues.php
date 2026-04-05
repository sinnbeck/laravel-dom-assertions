<?php

declare(strict_types=1);

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
