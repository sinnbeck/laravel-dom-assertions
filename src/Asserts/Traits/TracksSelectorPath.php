<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

trait TracksSelectorPath
{
    protected array $selectorPath = [];

    public function trackSelector(array $previousPath, string $selector): self
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
