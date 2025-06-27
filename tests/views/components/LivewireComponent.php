<?php

namespace Tests\Views\Components;

use Illuminate\View\Component;

class LivewireComponent extends Component
{
    public function __construct() {}

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <input wire:model="foo">
        </div>
        HTML;
    }
}
