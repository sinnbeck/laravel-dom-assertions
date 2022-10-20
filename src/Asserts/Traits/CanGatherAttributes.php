<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

trait CanGatherAttributes
{
    use InteractsWithParser;

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
                $attributes[$attribute->nodeName] = $attribute->value ?: true;
            }

            if ($type === 'textarea') {
                $extra['value'] = trim($element->nodeValue);
            }

            $extra['text'] = trim($element->nodeValue);

            $this->attributes[$type][] = $attributes + $extra;
        }
    }
}
