<?php

namespace Sinnbeck\DomAssertions\Asserts;

use PHPUnit\Framework\Assert;
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

    public function hasValue($value)
    {
        Assert::assertNotNull(
            $option = $this->makeScopedParser()->query('option[selected="selected"]'),
            'No options are selected!'
        );
        Assert::assertEquals(
            $value,
            $option->getAttribute('value')
        );

        return $this;
    }

    public function hasValues(array $values)
    {
        Assert::assertNotNull(
            $this->makeScopedParser()->query('option[selected="selected"]'),
            'No options are selected!'
        );

        foreach ($this->makeScopedParser()->queryAll('option[selected="selected"]') as $option) {
            $selected[] = $this->getAttributeFor($option, 'value');
        }

        Assert::assertEqualsCanonicalizing(
            $values,
            $selected,
            sprintf('Selected values does not match')
        );
    }
}
