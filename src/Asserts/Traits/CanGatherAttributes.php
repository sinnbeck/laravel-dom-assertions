<?php

namespace Sinnbeck\DomAssertions\Asserts\Traits;

trait CanGatherAttributes
{
    use InteractsWithParser;

    public function gatherAttributesWithText($type)
    {
        if (isset($this->attributes[$type])) {
            return;
        }

        $this->attributes[$type] = [];
        $elements = $this->parser->getElementsByType($type);

        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            $attributes = [];
            foreach ($element->attributes as $attribute) {
                $attributes[$attribute->nodeName] = $attribute->value;
            }
            $this->attributes[$type][] = $attributes + [
                'text' => trim($element->nodeValue),
            ];
        }
    }

    public function gatherAttributes($type)
    {
        if (isset($this->attributes[$type])) {
            return;
        }

        $this->attributes[$type] = [];

        $elements = $this->makeScopedParser()->queryAll($type);
        $extra = [];

        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            $attributes = [];
            foreach ($element->attributes as $attribute) {
                $attributes[$attribute->nodeName] = $attribute->value;
            }

            if ($type === 'textarea') {
                $extra['value'] = trim($element->nodeValue);
            }

            $extra['text'] = trim($element->nodeValue);

            $this->attributes[$type][] = $attributes + $extra;
        }
    }
}
