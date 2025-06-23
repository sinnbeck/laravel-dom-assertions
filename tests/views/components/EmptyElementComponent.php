<?php

namespace Tests\Views\Components;

use Illuminate\View\Component;

class EmptyElementComponent extends Component
{
    public function __construct() {}

    public function render(): string
    {
        return '<div></div>';
    }
}
