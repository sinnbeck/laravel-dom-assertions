<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions\Support;

use InvalidArgumentException;

/**
 * @internal
 */
final class Html
{
    /**
     * Matches a html element name.
     */
    private const ELEMENT_PATTERN = '/^[a-zA-Z][a-zA-Z0-9-]*$/';

    /**
     * Matches a html attribute name, including alpine and livewire syntax such as
     * "wire:model.live", "x-on:click" and ":bound".
     *
     * "@" is excluded deliberately. It is not valid in an XML name, so a name
     * containing it does not survive parsing: "@click" is dropped entirely and
     * "data@x" is mangled into a bare "data". Use the "x-on:" form instead.
     */
    private const ATTRIBUTE_PATTERN = '/^[a-zA-Z_:][a-zA-Z0-9_:.-]*$/';

    /**
     * @throws InvalidArgumentException
     */
    public static function element(string $name, string $content = '', array $attributes = []): string
    {
        $name = trim($name);

        if (! preg_match(self::ELEMENT_PATTERN, $name)) {
            throw new InvalidArgumentException(sprintf('Invalid element name: "%s"', $name));
        }

        return sprintf('<%s>%s</%s>', $name.self::attributes($attributes), $content, $name);
    }

    /**
     * Renders an attribute string. A value of true renders a bare attribute, false and
     * null are skipped, and attributes given without a key are rendered bare.
     *
     * @throws InvalidArgumentException
     */
    public static function attributes(array $attributes): string
    {
        $rendered = '';

        foreach ($attributes as $name => $value) {
            if (is_int($name)) {
                $name = $value;
                $value = true;
            }

            if (! is_string($name) || ! preg_match(self::ATTRIBUTE_PATTERN, $name)) {
                throw new InvalidArgumentException(
                    sprintf('Invalid attribute name: "%s"', is_string($name) ? $name : get_debug_type($name))
                );
            }

            if ($value === false || $value === null) {
                continue;
            }

            $rendered .= $value === true
                ? ' '.$name
                : sprintf(' %s="%s"', $name, htmlspecialchars((string) $value, ENT_QUOTES));
        }

        return $rendered;
    }
}
