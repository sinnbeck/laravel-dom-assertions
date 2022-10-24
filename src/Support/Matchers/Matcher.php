<?php

namespace Sinnbeck\DomAssertions\Support\Matchers;

interface Matcher
{
    public static function compare($value, $actual): bool;
}
