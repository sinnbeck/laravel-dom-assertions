<?php

namespace Sinnbeck\DomAssertions\Utilities\Matchers;

class NoValues implements Matcher
{
    public static function compare($expected, $actual): bool
    {
        return true;
    }
}
