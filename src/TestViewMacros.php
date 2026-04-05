<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions;

use Closure;
use Illuminate\Testing\TestView;
use Sinnbeck\DomAssertions\Concerns\ProvidesDomAssertionMacros;

/**
 * @internal
 *
 * @mixin TestView
 */
class TestViewMacros
{
    use ProvidesDomAssertionMacros;

    protected function content(): Closure
    {
        return static fn ($view): string => (string) $view;
    }

    protected function emptyMessage(): string
    {
        return 'The view is empty!';
    }
}
