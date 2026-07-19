<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions;

use Closure;
use Illuminate\Testing\TestComponent;

/**
 * @internal
 *
 * @mixin TestComponent
 */
class TestComponentMacros extends DomAssertionMacros
{
    public function emptyMessage(): string
    {
        return 'The component is empty!';
    }

    public function content(): Closure
    {
        return function (): string {
            /** @var TestComponent $this */
            return (string) $this;
        };
    }
}
