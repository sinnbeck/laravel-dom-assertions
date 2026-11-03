<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions\Traits;

use Closure;
use InvalidArgumentException;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\DomAssertionMacros;
use Sinnbeck\DomAssertions\Support\Html;
use Sinnbeck\DomAssertions\TestHtml;

/**
 * @internal
 *
 * @mixin DomAssertionMacros
 */
trait Wraps
{
    /**
     * @return Closure(string, array<array-key, mixed>=): TestHtml
     */
    public function wrap(): Closure
    {
        $emptyMessage = $this->emptyMessage();
        $content = $this->content();

        return function (string $element, array $attributes = []) use ($emptyMessage, $content): TestHtml {
            $html = $content->call($this);

            Assert::assertNotEmpty(
                $html,
                $emptyMessage
            );

            try {
                $wrapped = Html::element($element, $html, $attributes);
            } catch (InvalidArgumentException $exception) {
                Assert::fail($exception->getMessage());
            }

            return TestHtml::make($wrapped);
        };
    }
}
