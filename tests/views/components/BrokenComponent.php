<?php

namespace Tests\Views\Components;

use Illuminate\View\Component;

class BrokenComponent extends Component
{
    public function __construct() {}

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <div class="foo">
                <span></span>
                <span></span>
                <span><p></p>
                <table>
                    <td><div></div></td>
                </table>
        </div>
        HTML;
    }
}
