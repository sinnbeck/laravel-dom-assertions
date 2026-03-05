<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

/**
 * @internal
 */
trait CanGatherAttributes
{
    public function gatherAttributes($type): void
    {
        if (isset($this->attributes[$type])) {
            return;
        }

        $this->attributes[$type] = [];

        $elements = $this->getParser()->queryAll($type);
        $extra = [];

        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            $attributes = [];
            foreach ($element->attributes as $attribute) {
                $attributes[$attribute->nodeName] = $attribute->value;
            }

            $extra['text'] = $element->textContent;

            if ($type === 'textarea') {
                $extra['value'] = $element->textContent;
            }

            $this->attributes[$type][] = $attributes + $extra;
        }
    }

    abstract protected function getParser();
}
