<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

trait TrackSelectors
{
    protected array $selectorPath = [];

    public function withSelectors(array $previousPath, string $selector): self
    {
        $this->selectorPath = array_merge($previousPath, [$selector]);

        return $this;
    }

    public function getSelectors(): string
    {
        if (empty($this->selectorPath)) {
            return '(root)';
        }

        return implode(' > ', $this->selectorPath);
    }
}
