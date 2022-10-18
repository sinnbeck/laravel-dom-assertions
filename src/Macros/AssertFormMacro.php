<?php

namespace Sinnbeck\DomAssertions\Macros;

use Closure;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Asserts\FormAssert;
use Sinnbeck\DomAssertions\DomParser;
use Symfony\Component\VarDumper\VarDumper;

class AssertFormMacro
{
    public function __invoke()
    {
        return function (Closure $callback = null, $selector = null): TestResponse {
            /** @var TestResponse $this */
            if (is_null($selector)) {
                $selector = 0;
            }

            $parser = DomParser::new($this->getContent());

            if (is_int($selector)) {
                $form = $parser->getElementOfType('form', $selector);
            } elseif (is_string($selector)) {
                $form = $parser->query($selector);
            } else {
                Assert::fail('Invalid selector!');
            }

            if (is_null($form)){
                Assert::fail(sprintf('No form was found with selector: %s', $selector));
            }

            if ($form->nodeName !== 'form') {
                Assert::fail('Element is not of type form!');
            }

            if ($callback) {
                $callback(new FormAssert($this->getContent(), $form));

            }

            return $this;
        };
    }
}
