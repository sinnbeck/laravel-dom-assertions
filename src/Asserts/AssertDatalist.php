<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions\Asserts;

class AssertDatalist extends BaseAssert
{
    public function containsOption(mixed $attributes): self
    {
        $this->contains('option', $attributes);

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
