<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions;

use Closure;
use Illuminate\Testing\TestView;

/**
 * @internal
 *
 * @mixin TestView
 */
class TestViewMacros extends DomAssertionMacros
{
    public function emptyMessage(): string
    {
        return 'The view is empty!';
    }

    public function content(): Closure
    {
        return function (): string {
            /** @var TestView $this */
            return (string) $this;
        };
    }
}
