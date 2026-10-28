<?php

$response->assertElement('#nav', function (\Sinnbeck\DomAssertions\Asserts\AssertElement $element) {
    $element->find('a', function (\Sinnbeck\DomAssertions\Asserts\AssertElement $element) {
        $element->hasAttribute('class', 'active');
    });
});

?>
-----
<?php

$response->assertContainsElement('#nav a', ['class' => 'active']);

?>
