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
            <section><section/>
        </div>
        HTML;
    }
}
