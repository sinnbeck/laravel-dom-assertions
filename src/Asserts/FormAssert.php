<?php

namespace Sinnbeck\DomAssertions\Asserts;

use Illuminate\Support\Str;
use Illuminate\Testing\Assert as PHPUnit;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Asserts\Traits\CanGatherAttributes;
use Sinnbeck\DomAssertions\Asserts\Traits\HasElementAsserts;
use Sinnbeck\DomAssertions\DomParser;

class FormAssert
{
    use HasElementAsserts;
    use CanGatherAttributes;

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
        PHPUnit::assertEquals(
            Str::of($this->getAttributeFromForm('method'))->lower(),
            Str::of($method)->lower(),
            sprintf('Could not find a method on the form with the value %s', $method)
        );

        return $this;
    }

    public function containsTextarea(array $attributes): self
    {
        return $this->contains('textarea', $attributes);
    }

    public function containsInput(array $attributes): self
    {
        Assert::assertNotNull(
            $this->makeScopedParser()->query($this->getSelectorFromAttributes('input', $attributes)),
            sprintf('Could not find a matching input with data: %s', json_encode($attributes, JSON_PRETTY_PRINT))
        );

        return $this;

    }

    public function doesntContainInput(array $attributes, $name = ''): self
    {
        Assert::assertNull(
            $this->makeScopedParser()->query($this->getSelectorFromAttributes('input', $attributes)),
            sprintf('Found a matching input with data: %s', json_encode($attributes, JSON_PRETTY_PRINT))
        );

        return $this;
    }

    protected function getAttributeFromForm(string $attribute)
    {
        return $this->parser->getAttributeForRoot($attribute);
    }

    public function containsSelect(\Closure $callback, $selector = 'select'): static
    {
        if (!$select = $this->makeScopedParser()->query($selector)) {
            Assert::fail(sprintf('No select found for selector: %s', $selector));
        }
        $callback(new SelectAssert($this->parser->getContent(), $select));

        return $this;
    }

    public function hasCSRF(): self
    {
        Assert::assertNotNull(
            $this->parser->query('input[type="hidden"][name="_token"]'),
            'No CSRF was found in form!');

        return $this;
    }

    public function hasSpoofMethod(string $type): self
    {
        Assert::assertNotNull(
            $this->parser->query(sprintf('input[type="hidden"][name="_method"][value="%s"]', $type)),
            sprintf('No spoof method for %s was found in form!', $type));

        return $this;
    }

    protected function getSelectorFromAttributes($type, array $attributes): string
    {
        $selector = $type;

        foreach ($attributes as $attribute => $value) {
            $selector .= sprintf('[%s="%s"]', $attribute, $value);
        }

        return $selector;
    }

    protected function makeScopedParser(): DomParser
    {
        return $this->parser->cloneFromRoot()
            ->setRootFromString('form');
    }
}
