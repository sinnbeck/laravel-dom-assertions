<?php

namespace Tests\Views\Components;

use Illuminate\View\Component;

class EmptyComponent extends Component
{
    public function __construct() {}

    public function render(): string
    {
        return '';
    }
}
