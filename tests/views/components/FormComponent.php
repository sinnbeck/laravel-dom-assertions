<?php

namespace Tests\Views\Components;

use Illuminate\View\Component;

class FormComponent extends Component
{
    public function __construct() {}

    public function render(): string
    {
        return <<<'HTML'
        <form id="form1" x-data="foo" action="store-comment" enctype="multipart/form-data">
            <label for="comment">Comment</label>
            <textarea name="comment" id="comment" required>
                foo
            </textarea>
            
            <input type="checkbox" />

            <select name="things">
                <optgroup label="Animals">
                    <option value="dog">Dog</option>
                    <option value="cat">Cat</option>
                </optgroup>
                <optgroup label="Vegetables" x-data="none">
                    <option value="carrot">Carrot</option>
                    <option value="onion">Onion</option>
                </optgroup>
                <optgroup label="Minerals">
                    <option value="calcium">Calcium</option>
                    <option value="zinc">Zinc</option>
                </optgroup>
            </select>
        </form>
        HTML;
    }
}
