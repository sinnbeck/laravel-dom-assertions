<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

use Sinnbeck\DomAssertions\DomParser;

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
        return $this->getParser()->getAttributeForRoot($attribute);
    }

    protected function getAttributeFor($for, string $attribute)
    {
        return $this->getParser()->getAttributeFor($for, $attribute);
    }
}
