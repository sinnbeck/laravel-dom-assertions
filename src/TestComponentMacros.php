<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions;

use Closure;
use Illuminate\Testing\TestComponent;
use Sinnbeck\DomAssertions\Support\MacroTarget;
use Sinnbeck\DomAssertions\Traits\Wraps;

/**
 * @internal
 *
 * @mixin TestComponent
 */
class TestComponentMacros extends DomAssertionMacros
{
    use Wraps;

    public function emptyMessage(): string
    {
        return 'The component is empty!';
    }

    /**
     * @return Closure(): string
     */
    public function content(): Closure
    {
        return function (): string {
            return (string) MacroTarget::component($this);
        };
    }
}
