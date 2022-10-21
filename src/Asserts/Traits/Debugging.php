<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

trait Debugging
{
    public function dump(string $prop = null): self
    {
        dump($this->getParser()->getContent());

        return $this;
    }

    public function dd(string $prop = null): void
    {
        dd($this->getParser()->getContent());
    }

    abstract protected function getParser();
}
