<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions\Asserts;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Testing\Assert as PHPUnit;
use PHPUnit\Framework\Assert;

class AssertForm extends BaseAssert
{
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
        if (! in_array(strtolower($method), [
            'get',
            'post',
        ])) {
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
            sprintf('No spoof methods was found in form!')
        );

        Assert::assertEquals(
            Str::lower($type),
            Str::lower($this->getAttributeFor($element, 'value')),
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
        if ($selector instanceof Closure) {
            $callback = $selector;
            $selector = 'select';
        }

        if (! $select = $this->getParser()->query($selector)) {
            Assert::fail(sprintf('No select found for selector: %s', $selector));
        }

        $callback(new AssertSelect($this->getContent(), $select));

        return $this;
    }

    public function findDatalist($selector = 'datalist', $callback = null): static
    {
        if ($selector instanceof Closure) {
            $callback = $selector;
            $selector = 'datalist';
        }

        if ($selector !== 'datalist' && $selector[0] !== '#') {
            Assert::fail(sprintf('Selectors for datalists must be an id, given: %s', $selector));
        }

        if (! $select = $this->getParser()->query($selector)) {
            Assert::fail(sprintf('No datalist found for datalist: %s', $selector));
        }

        $callback(new AssertDatalist($this->getContent(), $select));

        return $this;
    }
}
