<?php

namespace Sinnbeck\DomAssertions\Asserts;

use PHPUnit\Framework\Assert;

class OptionAssert
{
    protected $options;

    protected $touched = [];

    protected $matched = [];

    public function __construct($options)
    {
        $this->options = collect($options);
    }

    public function hasValue($value): static
    {
        $this->touched['value'] = $value;
        if ($this->options->firstWhere('value', $value)) {
            $this->matched['value'] = $value;
        }

        return $this;
    }

    public function hasText($text): static
    {
        $this->touched['text'] = $text;
        if ($this->options->firstWhere('text', $text)) {
            $this->matched['text'] = $text;
        }

        return $this;
    }

    public function validate()
    {
        Assert::assertSame(
            [],
            array_diff($this->touched, $this->matched),
            sprintf('Could not find a matching option with data: %s', json_encode($this->touched, JSON_PRETTY_PRINT))
        );
    }
}
