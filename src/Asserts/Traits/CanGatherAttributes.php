<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

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
                $attributes[$attribute->nodeName] = $this->extractAttribute($attribute);
            }

            if ($type === 'textarea') {
                $extra['value'] = trim($element->nodeValue);
            }

            $extra['text'] = trim($element->nodeValue);

            $this->attributes[$type][] = $attributes + $extra;
        }
    }

    protected function extractAttribute(mixed $attribute): mixed
    {
        return $this->normalizeAttributeValue($attribute->nodeName, $attribute->value);
    }
}
