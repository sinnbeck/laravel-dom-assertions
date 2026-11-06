<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions\Support;

use Closure;

/**
 * @internal - Just for static analysis
 */
final class Bind
{
    /**
     * @template T of object
     *
     * @param  class-string<T>  $class
     *
     * @param-closure-this T  $macro
     */
    public static function to(string $class, Closure $macro): Closure
    {
        return $macro;
    }
}
