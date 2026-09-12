<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions\Traits;

use Closure;
use InvalidArgumentException;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Support\Html;
use Sinnbeck\DomAssertions\TestHtml;

/**
 * @internal
 *
 * @mixin \Sinnbeck\DomAssertions\DomAssertionMacros
 */
trait Wraps
{
    public function wrap(): Closure
    {
        $emptyMessage = $this->emptyMessage();

        return function (string $element, array $attributes = []) use ($emptyMessage): TestHtml {
            /** @var \Illuminate\Testing\TestComponent|TestHtml $this */
            Assert::assertNotEmpty(
                $this->content(),
                $emptyMessage
            );

            try {
                $html = Html::element($element, $this->content(), $attributes);
            } catch (InvalidArgumentException $exception) {
                Assert::fail($exception->getMessage());
            }

            return TestHtml::make($html);
        };
    }
}
