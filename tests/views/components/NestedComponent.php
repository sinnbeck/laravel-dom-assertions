<?php

declare(strict_types=1);

namespace Tests\Views\Components;

use Illuminate\View\Component;

class NestedComponent extends Component
{
    public function render(): string
    {
        return <<<'HTML'
        <div>
           <nav id="nav" data-id="42"><a href="/foo">Foo</a></nav>
            <div>
                <span class="bar foo">Foo</span>
                <div class="foobar">
                    <div x-data="foobar">
                        <div class="deep">
                            <span></span>
                        </div>
                    </div>
                    <ul>
                        <li x-data="foobar" data-id="1"></li>
                        <li x-data="foobar" data-id="2"></li>
                    </ul>
                </div>
                <p class="foo bar">
                    Foo
                    <span>Bar</span>
                </p>
                <pre class="code">line one
        line two
        line three</pre>
            </div>
        </div>
        HTML;
    }
}
