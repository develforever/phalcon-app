<?php

declare(strict_types=1);

namespace Tests\Unit;

class UnitTest extends \PHPUnit\Framework\TestCase
{
    public function testTestCase(): void
    {
        $this->assertEquals(
            "roman",
            "roman",
            "This will pass"
        );
    }
}
