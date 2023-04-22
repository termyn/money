<?php

declare(strict_types=1);

namespace Termyn\Test\Currency\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Termyn\Currency\Unit\Subunit;

final class SubunitTest extends TestCase
{
    #[Test]
    public function shouldBeCreated(): void
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

    #[Test]
    public function throwsExceptionIfPrecisionIsOutOfRange(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Subunit('cent', 'c', 2);
    }

    #[Test]
    public function canBeEqual(): void
    {
        $cent = new Subunit('cent', 'c', 100);
        $penny = new Subunit('penny', 'p', 10);

        $this->assertTrue($cent->equals($cent));
        $this->assertFalse($cent->equals($penny));
    }

    #[Test]
    public function returnsExpectedPrecision(): void
    {
        $data = [
            0 => 1,
            1 => 10,
            2 => 100,
            3 => 1000,
        ];

        foreach ($data as $precision => $fraction) {
            $cent = new Subunit('cent', 'c', $fraction);

            $this->assertEquals($precision, $cent->precision());
        }
    }
}
