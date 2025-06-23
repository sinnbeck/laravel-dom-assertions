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
            </head>
            <body>
            </body>
        </html>
        HTML;
    }
}
