<?php

namespace Tests\Views\Components;

use Illuminate\View\Component;

class EmptyBodyComponent extends Component
{
    public function __construct() {}

    public function render(): string
    {
        return <<<'HTML'
        <!doctype html>
        <html lang="en">
            <head>
            </head>
        </html>
        HTML;
    }
}
