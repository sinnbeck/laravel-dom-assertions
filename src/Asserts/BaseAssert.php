<?php

namespace Sinnbeck\DomAssertions\Asserts;

use Sinnbeck\DomAssertions\Asserts\Traits\CanGatherAttributes;
use Sinnbeck\DomAssertions\Asserts\Traits\Debugging;
use Sinnbeck\DomAssertions\Asserts\Traits\InteractsWithParser;
use Sinnbeck\DomAssertions\Asserts\Traits\NormalizesData;
use Sinnbeck\DomAssertions\Asserts\Traits\UsesElementAsserts;
use Sinnbeck\DomAssertions\Parsers\DomParser;

abstract class BaseAssert
{
    use UsesElementAsserts;
    use CanGatherAttributes;
    use InteractsWithParser;
    use NormalizesData;
    use Debugging;

    public function __construct($html, $element = null)
    {
        $this->html = $html;
        $this->parser = DomParser::new($html);

        if (! is_null($element)) {
            $this->parser->setRoot($element);
        }
    }
}
