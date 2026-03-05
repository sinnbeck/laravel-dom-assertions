<?php

use Livewire\Livewire;
use Tests\Views\Components\LivewireComponent;

it('can find an element by selector after performing update', function () {
    Livewire::test(LivewireComponent::class)
        ->assertOk()
        ->assertElementExists('#nav')
        ->update()
        ->assertElementExists('#nav');
});
