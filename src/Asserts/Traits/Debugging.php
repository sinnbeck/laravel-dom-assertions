<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

/**
 * @internal
 */
trait Debugging
{
    public function dump(): self
    {
        dump($this->getParser()->getContentFormatted());

        return $this;
    }

    public function dd(): void
    {
        dd($this->getParser()->getContentFormatted());
    }

    abstract protected function getParser();
}
