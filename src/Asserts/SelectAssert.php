<?php

namespace Sinnbeck\DomAssertions\Asserts;

use Sinnbeck\DomAssertions\Asserts\Traits\CanGatherAttributes;
use Sinnbeck\DomAssertions\Asserts\Traits\HasElementAsserts;
use Sinnbeck\DomAssertions\Asserts\Traits\InteractsWithParser;
use Sinnbeck\DomAssertions\DomParser;

class SelectAssert
{
    use HasElementAsserts;
    use CanGatherAttributes;
    use InteractsWithParser;

    protected DomParser $parser;

    protected array $attributes = [];

    public function __construct(string $html, $root)
    {
        $this->parser = DomParser::new($html)
            ->setRoot($root);
    }

    public function containsOption(mixed $attributes): self
    {
        $this->gatherAttributesWithText('option');

        if (is_array($attributes)) {
            $this->contains('option', $attributes);
        }

        if (is_callable($attributes)) {
            tap(
                new OptionAssert(
                    $this->attributes['option']
                ), fn ($option) => $attributes($option)
            )->validate();
        }

        return $this;
    }

    public function containsOptions(...$attributes): self
    {
        foreach ($attributes as $attribute) {
            $this->containsOption($attribute);
        }

        return $this;
    }
}
