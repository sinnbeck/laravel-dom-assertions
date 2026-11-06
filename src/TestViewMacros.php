<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions;

use Closure;
use Illuminate\Testing\TestView;
use Sinnbeck\DomAssertions\Support\Bind;

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
        return Bind::to(TestView::class, function (): string {
            return (string) $this;
        });
    }
}
