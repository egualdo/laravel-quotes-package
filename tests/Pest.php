<?php

use Vendor\Quotes\Tests\TestCase;

// Solo los tests de Feature necesitan TestCase de Laravel
uses(TestCase::class)->in('Feature');
// Los tests Unit NO necesitan TestCase