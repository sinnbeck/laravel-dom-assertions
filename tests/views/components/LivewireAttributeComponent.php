<?php

declare(strict_types=1);

namespace Tests\Views\Components;

use Illuminate\View\Component;

class LivewireAttributeComponent extends Component
{
    public function render(): string
    {
        return <<<'HTML'
        <div>
            <input wire:model="foo">
        </div>
        HTML;
    }
}
