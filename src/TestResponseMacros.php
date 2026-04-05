<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions;

use Closure;
use DOMException;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;
use Sinnbeck\DomAssertions\Concerns\ProvidesDomAssertionMacros;
use Sinnbeck\DomAssertions\Support\DomParser;

/**
 * @internal
 *
 * @mixin TestResponse
 */
class TestResponseMacros
{
    use ProvidesDomAssertionMacros;

    protected function content(): Closure
    {
        return static fn ($response): string => $response->getContent();
    }

    protected function emptyMessage(): string
    {
        return 'The response is empty!';
    }

    /**
     * @internal
     */
    protected function getDomParser(): Closure
    {
        $content = $this->content();

        return function () use ($content): DomParser {
            /** @var TestResponse $this */
            $html = $content($this);

            // Due to being the test response, livewire users can access the DOM assertions.
            // If the component is updated and the content-type is json, we attempt to render the html.
            if ($this->headers->get('content-type') === 'application/json') {
                $json = json_decode($html, true);
                if (isset($json['components'][0]['effects']['html'])) {
                    $html = $json['components'][0]['effects']['html'];
                }
            }

            $hash = hash('xxh128', $html);
            $cacheKey = 'dom-assertions.parser.'.$hash;

            if (! app()->has($cacheKey)) {
                try {
                    app()->instance($cacheKey, DomParser::new($html));
                } catch (DOMException $exception) {
                    Assert::fail($exception->getMessage());
                }
            }

            return app()->make($cacheKey);
        };
    }
}
