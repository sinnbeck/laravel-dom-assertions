<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions;

use Closure;
use Sinnbeck\DomAssertions\Support\MacroTarget;
use Sinnbeck\DomAssertions\Traits\Wraps;

/**
 * @internal
 *
 * @mixin TestHtml
 */
class TestHtmlMacros extends DomAssertionMacros
{
    use Wraps;

    public function emptyMessage(): string
    {
        return 'The html is empty!';
    }

    /**
     * @return Closure(): string
     */
    public function content(): Closure
    {
        return function (): string {
            return (string) MacroTarget::html($this);
        };
    }
}
