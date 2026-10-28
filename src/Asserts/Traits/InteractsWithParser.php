<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

use Sinnbeck\DomAssertions\Support\DomParser;

/**
 * @internal
 */
trait InteractsWithParser
{
    protected DomParser $parser;

    public function getParser(): DomParser
    {
        return $this->parser;
    }

    protected function getContent(): string
    {
        return $this->getParser()->getContent();
    }

    protected function getAttribute(string $attribute): string
    {
        if ($attribute === 'text') {
            return $this->getParser()->getText();
        }

        return $this->getParser()->getAttributeForRoot($attribute);
    }

    protected function attributeExists(string $attribute): bool
    {
        return $this->getParser()->hasAttributeForRoot($attribute);
    }

    protected function getAttributeFor($for, string $attribute)
    {
        return $this->getParser()->getAttributeFor($for, $attribute);
    }
}
