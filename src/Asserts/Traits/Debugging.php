<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

trait Debugging
{
    public function dump(): self
    {
        dump($this->getParser()->getContent());

        return $this;
    }

    public function dd(): void
    {
        dd($this->getParser()->getContent());
    }

    abstract protected function getParser();
}
