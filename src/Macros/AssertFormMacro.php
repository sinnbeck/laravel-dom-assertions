<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions\Macros;

use DOMException;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Asserts\FormAssert;
use Sinnbeck\DomAssertions\Support\DomParser;

class AssertFormMacro
{
    public function __invoke(): \Closure
    {
        return function ($selector = 'form', $callback = null): TestResponse {
            /** @var TestResponse $this */
            Assert::assertNotEmpty(
                $this->getContent(),
                'The view is empty!'
            );

            try {
                $parser = DomParser::new($this->getContent());
            } catch (DOMException $exception) {
                Assert::fail($exception->getMessage());
            }

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
