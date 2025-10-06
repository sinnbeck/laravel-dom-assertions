<?php

namespace Sinnbeck\DomAssertions\Asserts;

use BadMethodCallException;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Sinnbeck\DomAssertions\Asserts\Traits\CanGatherAttributes;
use Sinnbeck\DomAssertions\Asserts\Traits\Debugging;
use Sinnbeck\DomAssertions\Asserts\Traits\InteractsWithParser;
use Sinnbeck\DomAssertions\Asserts\Traits\TrackSelectors;
use Sinnbeck\DomAssertions\Asserts\Traits\UsesElementAsserts;
use Sinnbeck\DomAssertions\Support\DomParser;

/**
 * @internal
 */
abstract class BaseAssert
{
    use CanGatherAttributes;
    use Debugging;
    use InteractsWithParser;
    use Macroable {
        __call as protected callMacro;
    }
    use TrackSelectors;
    use UsesElementAsserts;

    protected array $attributes = [];

    public function __construct($html, $element = null)
    {
        $this->parser = DomParser::new($html);

        if (! is_null($element)) {
            $this->parser->setRoot($element);
        }
    }

    public function __call($method, $arguments)
    {
        if (static::hasMacro($method)) {
            return $this->callMacro($method, $arguments);
        }
        if (Str::startsWith($method, 'has')) {
            $property = Str::of($method)->after('has')->snake()->slug();

            return $this->has($property, $arguments[0] ?? null);
        }

        if (Str::startsWith($method, 'is')) {
            $property = Str::of($method)->after('is')->snake()->slug();

            return $this->is($property);
        }

        if (Str::startsWith($method, 'find')) {
            $property = Str::of($method)->after('find')->snake()->slug();

            return $this->find($property, $arguments[0] ?? null);
        }

        if (Str::startsWith($method, 'contains')) {
            $elementName = Str::of($method)->after('contains')->camel();

            return $this->contains($elementName, ...$arguments);
        }

        if (Str::startsWith($method, 'doesntContain')) {
            $elementName = Str::of($method)->after('doesntContain')->camel();

            return $this->doesntContain($elementName, ...$arguments);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method %s::%s()', static::class, $method));
    }
}
