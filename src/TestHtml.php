<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions;

use Illuminate\Support\Traits\Macroable;
use Stringable;

/**
 * A rendered piece of html that can be asserted on.
 *
 * @method $this assertHtml5()
 * @method $this assertElement($selector = 'body', $callback = null)
 * @method $this assertElementExists($selector = 'body', $callback = null)
 * @method $this assertForm($selector = 'form', $callback = null)
 * @method $this assertFormExists($selector = 'form', $callback = null)
 * @method $this assertSelect($selector = 'select', $callback = null)
 * @method $this assertSelectExists($selector = 'select', $callback = null)
 * @method $this assertContainsElement(string $selector, array $attributes = [])
 * @method $this assertDoesntExist(string $selector)
 * @method $this assertElementContainsText(string $selector, string $needle, bool $ignoreCase = false, ?bool $normalizeWhitespace = null)
 * @method $this assertElementContainsNormalizedText(string $selector, string $needle, bool $ignoreCase = false)
 * @method self wrap(string $element, array $attributes = [])
 * @method string content()
 * @method void ddContent()
 */
class TestHtml implements Stringable
{
    use Macroable;

    public function __construct(protected string $html) {}

    public static function make(string $html): self
    {
        return new self($html);
    }

    public function __toString(): string
    {
        return $this->html;
    }
}
