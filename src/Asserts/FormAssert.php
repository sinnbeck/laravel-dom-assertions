<?php

namespace Sinnbeck\DomAssertions\Asserts;

use Illuminate\Support\Str;
use Illuminate\Testing\Assert as PHPUnit;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Asserts\Traits\CanGatherAttributes;
use Sinnbeck\DomAssertions\Asserts\Traits\HasElementAsserts;
use Sinnbeck\DomAssertions\Asserts\Traits\InteractsWithParser;
use Sinnbeck\DomAssertions\DomParser;

class FormAssert
{
    use HasElementAsserts;
    use CanGatherAttributes;
    use InteractsWithParser;

    /**
     * @var \Sinnbeck\DomAssertions\DomParser
     */
    protected DomParser $parser;

    protected string $html;

    protected array $attributes = [];

    public function __construct($html, $form)
    {
        $this->html = $html;
        $this->parser = DomParser::new($html)
            ->setRoot($form);
    }

    public function setParser($parser): static
    {
        $this->parser = $parser;

        return $this;
    }

    public function hasAction(string $action): self
    {
        PHPUnit::assertEquals(
            Str::of($this->getAttributeFromForm('action'))->lower()->finish('/')->start('/'),
            Str::of($action)->lower()->finish('/')->start('/'),
            sprintf('Could not find an action on the form with the value %s', $action)
        );

        return $this;
    }

    public function hasMethod(string $method): self
    {
        if (! in_array(strtolower($method), ['get', 'post'])) {
            return $this->hasSpoofMethod($method);
        }
        PHPUnit::assertEquals(
            Str::of($this->getAttributeFromForm('method'))->lower(),
            Str::of($method)->lower(),
            sprintf('Could not find a method on the form with the value %s', $method)
        );

        return $this;
    }

    public function hasSpoofMethod(string $type): self
    {
        $element = $this->parser->query('input[type="hidden"][name="_method"]');
        Assert::assertNotNull(
            $element,
            sprintf('No spoof methods was found in form!', $type)
        );

        Assert::assertEquals(
            $type,
            $this->getAttributeFor($element, 'value'),
            sprintf('No spoof method for %s was found in form!', $type)
        );

        return $this;
    }

    public function hasCSRF(): self
    {
        Assert::assertNotNull(
            $this->parser->query('input[type="hidden"][name="_token"]'),
            'No CSRF was found in form!');

        return $this;
    }

    protected function getAttributeFromForm(string $attribute)
    {
        return $this->parser->getAttributeForRoot($attribute);
    }

    public function findSelect($selector = 'select', $callback = null): static
    {
        if (is_callable($selector)) {
            $callback = $selector;
            $selector = 'select';
        }

        if (! $select = $this->getParser()->query($selector)) {
            Assert::fail(sprintf('No select found for selector: %s', $selector));
        }

        $callback(new SelectAssert($this->getContent(), $select));

        return $this;
    }

//    protected function getSelectorFromAttributes($type, array $attributes): string
//    {
//        $selector = $type;
//
//        foreach ($attributes as $attribute => $value) {
//            $selector .= sprintf('[%s="%s"]', $attribute, $value);
//        }
//
//        return $selector;
//    }
}
