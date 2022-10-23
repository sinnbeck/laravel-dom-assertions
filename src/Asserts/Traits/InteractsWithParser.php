<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

use Sinnbeck\DomAssertions\Utilities\DomParser;

trait InteractsWithParser
{
    protected function getParser(): DomParser
    {
        return $this->parser;
    }

    protected function getContent()
    {
        return $this->getParser()->getContent();
    }

    protected function getAttribute(string $attribute)
    {
        if ($this->getParser()->getType() === 'option' && $attribute === 'text') {
            return $this->getParser()->getText();
        }

        return $this->getParser()->getAttributeForRoot($attribute);
    }

    protected function hasAttribute(string $attribute)
    {
        return $this->getParser()->hasAttributeForRoot($attribute);
    }

    protected function getAttributeFor($for, string $attribute)
    {
        return $this->getParser()->getAttributeFor($for, $attribute);
    }
}
