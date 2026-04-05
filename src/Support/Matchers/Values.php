<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions\Support\Matchers;

/**
 * @internal
 */
class Values implements Matcher
{
    public static function compare($expected, $actual): bool
    {
        return $expected === $actual;
    }
}
