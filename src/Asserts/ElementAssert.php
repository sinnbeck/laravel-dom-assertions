<?php

namespace Sinnbeck\DomAssertions\Asserts;

use Illuminate\Foundation\Testing\Concerns\InteractsWithContainer;
use Sinnbeck\DomAssertions\Asserts\Traits\CanGatherAttributes;
use Sinnbeck\DomAssertions\Asserts\Traits\HasElementAsserts;
use Sinnbeck\DomAssertions\DomParser;

class ElementAssert
{
    use HasElementAsserts;
    use CanGatherAttributes;
    use InteractsWithContainer;

    /**
     * @var \Sinnbeck\DomAssertions\DomParser
     */
    protected DomParser $parser;

    protected string $html;

    protected array $attributes = [];

    public function __construct($html, $element = null)
    {
        $this->html = $html;
        $this->parser = DomParser::new($html);

        if (! is_null($element)) {
            $this->parser->setRoot($element);
        }
    }
}
