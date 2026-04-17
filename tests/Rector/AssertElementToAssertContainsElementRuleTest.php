<?php

declare(strict_types=1);

namespace Tests\Rector;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class AssertElementToAssertContainsElementRuleTest extends AbstractRectorTestCase
{
    public function test(): void
    {
        $this->doTestFile(__DIR__.'/Fixture/assert_element_to_assert_contains_element.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/rector.php';
    }
}
