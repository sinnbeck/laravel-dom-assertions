<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Sinnbeck\DomAssertions\Rector\Rules\AssertElementToAssertContainsElementRule;

return RectorConfig::configure()
    ->withRules([
        AssertElementToAssertContainsElementRule::class,
    ]);
