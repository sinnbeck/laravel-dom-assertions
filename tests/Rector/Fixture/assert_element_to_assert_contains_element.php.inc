<?php

$response->assertElement('#nav', function (\Sinnbeck\DomAssertions\Asserts\AssertElement $element) {
    $element->contains('a', ['class' => 'active']);
});

?>
-----
<?php

$response->assertContainsElement('#nav a', ['class' => 'active']);

?>
