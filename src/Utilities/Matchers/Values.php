<?php

namespace Sinnbeck\DomAssertions\Utilities\Matchers;

class Values implements Matcher
{
    public static function compare($expected, $actual): bool
    {
        return $expected === $actual;
    }
}
