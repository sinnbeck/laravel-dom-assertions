<?php

declare(strict_types=1);

namespace Tests\Views\Components;

use Illuminate\View\Component;

class OptionsComponent extends Component
{
    public function render(): string
    {
        return <<<'HTML'
        <option value="1">Option 1</option>
        <option value="2" selected="selected">Option 2</option>
        <option value="3">Option 3</option>
        HTML;
    }
}
