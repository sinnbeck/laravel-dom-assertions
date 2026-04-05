<?php

declare(strict_types=1);

namespace Tests\Views\Components;

use Livewire\Component;

class LivewireComponent extends Component
{
    public function render(): string
    {
        return <<<'HTML'
        <div>
            <nav id="nav"><a href="/foo">Foo</a></nav>
            <input wire:model="foo">
        </div>
        HTML;
    }
}
