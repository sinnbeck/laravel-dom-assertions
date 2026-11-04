<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions\Support;

use Illuminate\Testing\TestComponent;
use Illuminate\Testing\TestResponse;
use Illuminate\Testing\TestView;
use LogicException;
use Sinnbeck\DomAssertions\TestHtml;

/**
 * Narrows the instance Laravel rebinds the macro closures to.
 *
 * @internal
 */
final class MacroTarget
{
    public static function from(object $instance): TestComponent|TestHtml|TestResponse|TestView
    {
        return $instance instanceof TestComponent || $instance instanceof TestHtml || $instance instanceof TestResponse || $instance instanceof TestView
            ? $instance
            : self::fail($instance, TestComponent::class, TestHtml::class, TestResponse::class, TestView::class);
    }

    public static function component(object $instance): TestComponent
    {
        return $instance instanceof TestComponent
            ? $instance
            : self::fail($instance, TestComponent::class);
    }

    public static function html(object $instance): TestHtml
    {
        return $instance instanceof TestHtml
            ? $instance
            : self::fail($instance, TestHtml::class);
    }

    public static function response(object $instance): TestResponse
    {
        return $instance instanceof TestResponse
            ? $instance
            : self::fail($instance, TestResponse::class);
    }

    public static function view(object $instance): TestView
    {
        return $instance instanceof TestView
            ? $instance
            : self::fail($instance, TestView::class);
    }

    /**
     * @param  class-string  ...$expected
     */
    private static function fail(object $instance, string ...$expected): never
    {
        throw new LogicException(sprintf(
            'DOM assertions can only be used on %s. Got %s.',
            implode(', ', $expected),
            $instance::class,
        ));
    }
}
