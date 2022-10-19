<?php

namespace Sinnbeck\DomAssertions\Macros;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Asserts\FormAssert;
use Sinnbeck\DomAssertions\DomParser;

class AssertFormMacro
{
    public function __invoke()
    {
        return function ($selector = 'form', $callback = null): TestResponse {
            /** @var TestResponse $this */
            $parser = DomParser::new($this->getContent());

            if (is_callable($selector)) {
                $callback = $selector;
                $selector = 'form';
            }

            if (is_string($selector)) {
                $form = $parser->query($selector);
            } else {
                Assert::fail('Invalid selector!');
            }

            Assert::assertNotNull(
                $form,
                sprintf('No form was found with selector "%s"', $selector)
            );
            Assert::assertEquals(
                'form',
                $form->nodeName,
                'Element is not of type form!');

            if ($callback) {
                $callback(new FormAssert($this->getContent(), $form));
            }

            return $this;
        };
    }
}
