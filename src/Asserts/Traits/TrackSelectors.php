<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

trait TrackSelectors
{
    protected array $selectorPath = [];

    public function withSelectors(array $previousSelectors, string $currentSelector): self
    {
        $this->selectorPath = array_merge($previousSelectors, [$currentSelector]);

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
