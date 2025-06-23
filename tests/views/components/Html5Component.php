<?php

namespace Tests\Views\Components;

use Illuminate\View\Component;

class Html5Component extends Component
{
    public function __construct() {}

    public function render(): string
    {
        return <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport"
                      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                <meta http-equiv="X-UA-Compatible" content="ie=edge">
                <title>Nesting</title>
            </head>
            <body>
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
            </body>
        </html>
        HTML;
    }
}
