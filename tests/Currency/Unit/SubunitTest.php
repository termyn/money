<?php

declare(strict_types=1);

namespace Termyn\Test\Currency\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Termyn\Currency\Unit\Subunit;

final class SubunitTest extends TestCase
{
    public function testCreation(): void
    {
        $code = 'cent';
        $symbol = 'c';
        $fractions = [1, 10, 100, 1000];

        foreach ($fractions as $fraction) {
            $subunit = new Subunit($code, $symbol, $fraction);

            $this->assertEquals($code, $subunit->code);
            $this->assertEquals($symbol, $subunit->symbol);
            $this->assertEquals($fraction, $subunit->fraction);
        }
    }
    
    public function testExceptionIfPrecisionIsOutOfRange(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Subunit('cent', 'c', 2);
    }

    public function testEquality(): void
    {
        $cent = new Subunit('cent', 'c', 100);
        $penny = new Subunit('penny', 'p', 10);

        $this->assertTrue($cent->equals($cent));
        $this->assertFalse($cent->equals($penny));
    }

    public function testPrecision(): void
    {
        $cent = new Subunit('cent', 'c', 100);

        $this->assertEquals(2, $cent->precision());
    }
}