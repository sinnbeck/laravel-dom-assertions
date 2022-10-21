<?php

namespace Sinnbeck\DomAssertions\Macros;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Asserts\ElementAssert;
use Sinnbeck\DomAssertions\Parsers\DomParser;

class AssertElementMacro
{
    public function __invoke()
    {
        return function ($selector = 'body', $callback = null): TestResponse {
            /** @var TestResponse $this */
            $parser = DomParser::new($this->getContent());

            if (is_callable($selector)) {
                $callback = $selector;
                $selector = 'body';
            }

            if (is_string($selector)) {
                $element = $parser->query($selector);
            } else {
                Assert::fail('Invalid selector!');
            }

            Assert::assertNotNull($element, sprintf('No element found with selector: %s', $selector));

            if ($callback) {
                $callback(new ElementAssert($this->getContent(), $element));
            }

            return $this;
        };
    }
}
