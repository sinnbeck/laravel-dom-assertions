<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions;

use Closure;
use Illuminate\Testing\TestView;
use Sinnbeck\DomAssertions\Support\MacroTarget;

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

    /**
     * @return Closure(): string
     */
    public function content(): Closure
    {
        return function (): string {
            return (string) MacroTarget::view($this);
        };
    }
}
