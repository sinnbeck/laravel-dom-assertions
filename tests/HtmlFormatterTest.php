<?php

use Sinnbeck\DomAssertions\Support\HtmlFormatter;

it('can format a basic html document', function () {
    $html = getFixture('basic.html');

    $expected = getFixture('basic-expected.html');
    $formatted = (new HtmlFormatter)->format($html);

    $this->assertEquals($expected, $formatted);
});

it('can format an advanced html document', function () {
    $html = getFixture('advanced.html');

    $expected = getFixture('advanced-expected.html');
    $formatted = (new HtmlFormatter)->format($html);

    $this->assertEquals($expected, $formatted);
});
