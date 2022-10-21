<?php

namespace Sinnbeck\DomAssertions\Asserts;

use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Asserts\Traits\CanGatherAttributes;
use Sinnbeck\DomAssertions\Asserts\Traits\Debugging;
use Sinnbeck\DomAssertions\Asserts\Traits\NormalizesData;
use Sinnbeck\DomAssertions\Asserts\Traits\UsesElementAsserts;
use Sinnbeck\DomAssertions\Asserts\Traits\InteractsWithParser;
use Sinnbeck\DomAssertions\Parsers\DomParser;

class SelectAssert extends BaseAssert
{
    public function containsOption(mixed $attributes): self
    {
        return $this->contains('option', $attributes);
    }

    public function containsOptions(...$attributes): self
    {
        foreach ($attributes as $attribute) {
            $this->containsOption($attribute);
        }

        return $this;
    }

    public function hasValue($value): self
    {
        Assert::assertNotNull(
            $option = $this->getParser()->query('option[selected="selected"]'),
            'No options are selected!'
        );

        Assert::assertEquals(
            $value,
            $option->getAttribute('value')
        );

        return $this;
    }

    public function hasValues(array $values): self
    {
        Assert::assertNotNull(
            $this->getParser()->query('option[selected="selected"]'),
            'No options are selected!'
        );

        $selected = [];
        foreach ($this->getParser()->queryAll('option[selected="selected"]') as $option) {
            $selected[] = $this->getAttributeFor($option, 'value');
        }

        Assert::assertEqualsCanonicalizing(
            $values,
            $selected,
            sprintf('Selected values does not match')
        );

        return $this;
    }
}
