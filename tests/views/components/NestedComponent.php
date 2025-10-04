<?php

namespace Tests\Views\Components;

use Illuminate\View\Component;

class NestedComponent extends Component
{
    public function __construct() {}

    public function render(): string
    {
        return <<<'HTML'
        <div>
           <nav id="nav"><a href="/foo">Foo</a></nav>
            <div>
                <span class="bar foo">Foo</span>
                <div class="foobar">
                    <div x-data="foobar">
                        <div class="deep">
                            <span></span>
                        </div>
                    </div>
                    <ul>
                        <li x-data="foobar"></li>
                        <li x-data="foobar"></li>
                    </ul>
                </div>
                <p class="foo bar">
                    Foo
                    <span>Bar</span>
                </p>
            </div>
        </div>
        HTML;
    }
}
