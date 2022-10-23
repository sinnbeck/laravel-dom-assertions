<?php

namespace Sinnbeck\DomAssertions\Utilities\Matchers;

interface Matcher
{
    public static function compare($value, $actual);
}
