<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions;

use Closure;
use Illuminate\Testing\TestResponse;

/**
 * @internal
 *
 * @mixin TestResponse
 */
class TestResponseMacros extends DomAssertionMacros
{
    public function emptyMessage(): string
    {
        return 'The response is empty!';
    }

    public function content(): Closure
    {
        return function (): string {
            /** @var TestResponse $this */
            $content = $this->getContent();

            // Due to being the test response, livewire users can access the DOM assertions.
            // If the component is updated and the content-type is json, we attempt to render the html.
            if ($this->headers->get('content-type') === 'application/json') {
                $json = json_decode($content, true);
                if (isset($json['components'][0]['effects']['html'])) {
                    $content = $json['components'][0]['effects']['html'];
                }
            }

            return $content;
        };
    }
}
