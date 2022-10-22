<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

use Sinnbeck\DomAssertions\Formatters\Normalize;

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
                $attributes[$attribute->nodeName] = Normalize::attributeValue($attribute->nodeName, $attribute->value);
            }

            $extra['text'] = Normalize::attributeValue('text', $element->nodeValue);

            if ($type === 'textarea') {
                $extra['value'] = $extra['text'];
            }

            $this->attributes[$type][] = $attributes + $extra;
        }
    }
}
