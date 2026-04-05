<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions;

use Closure;
use Illuminate\Testing\TestComponent;
use Sinnbeck\DomAssertions\Concerns\ProvidesDomAssertionMacros;

/**
 * @internal
 *
 * @mixin TestComponent
 */
class TestComponentMacros
{
    use ProvidesDomAssertionMacros;

    protected function content(): Closure
    {
        return static fn ($component): string => (string) $component;
    }

    protected function emptyMessage(): string
    {
        return 'The component is empty!';
    }
}
