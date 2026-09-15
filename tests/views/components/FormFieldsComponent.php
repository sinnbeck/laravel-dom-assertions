<?php

declare(strict_types=1);

namespace Tests\Views\Components;

use Illuminate\View\Component;

class FormFieldsComponent extends Component
{
    public function render(): string
    {
        return <<<'HTML'
        <input name="email" type="email" value="foo@bar.com">
        <textarea name="message">Hello</textarea>
        HTML;
    }
}
