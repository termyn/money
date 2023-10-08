<?php

declare(strict_types=1);

namespace Termyn\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Termyn\Currency\Euro;
use Termyn\Currency\UsDollar;
use Termyn\InvalidMoneyString;
use Termyn\Money;
use Termyn\Money\MismatchCurrencies;

final class MoneyTest extends TestCase
{
    private Euro $euro;

    private UsDollar $usDollar;

    protected function setUp(): void
    {
        $this->euro = new Euro();
        $this->usDollar = new UsDollar();

        parent::setUp();
    }

    #[Test]
    public function shouldBeCreatedBasedOnSuperunit(): void
    {
        $amount = 1.20;

        $money = Money::of($amount, $this->euro);

        $this->assertEquals($amount, $money->amount);
        $this->assertEquals($this->euro, $money->currency);
    }

    #[Test]
    public function shouldBeCreatedBasedOnSubunit(): void
    {
        $amount = 1.20;

        $money = Money::ofSub(intval($amount * 100), $this->euro);

        $this->assertEquals($amount, $money->amount);
        $this->assertEquals($this->euro, $money->currency);
    }

    #[Test]
    #[DataProvider('provideValidStrings')]
    public function shouldBeCreatedFromString(string $string, float $amount): void
    {
        $money = Money::from($string);

        $this->assertEquals($amount, $money->amount);
        $this->assertEquals($this->euro, $money->currency);
    }

    public static function provideValidStrings(): array
    {
        $amounts = [0, 0.00, 1.25, 1500];
        $strings = array_values(
            array_map(
                fn (int|float $amount): array => [
                    [sprintf('€%s', $amount), $amount],
                    [sprintf('-€%s', $amount), -1 * $amount],
                ],
                $amounts,
            )
        );

        return array_merge(...$strings);
    }

    #[Test]
    #[DataProvider('provideInvalidStrings')]
    public function throwsExceptionOnInvalidString(string $money): void
    {
        $this->expectException(InvalidMoneyString::class);

        Money::from($money);
    }

    public static function provideInvalidStrings(): array
    {
        return [
            ['0'], ['0.00'], ['125'], ['$1 250,00'], ['$1,250.50'],
        ];
    }

    #[Test]
    public function canBeCompared(): void
    {
        $origin = Money::of(1.2, $this->euro);

        $comparable = Money::of(2.2, $this->euro);
        $notComparable = Money::of(1.2, $this->usDollar);

        $this->assertTrue($origin->isComparable($comparable));
        $this->assertFalse($origin->isComparable($notComparable));
    }

    #[Test]
    public function canBeEqual(): void
    {
        $origin = Money::of(1.2, $this->euro);
        $same = Money::of(1.2, $this->euro);
        $similar = Money::of(2.2, $this->euro);

        $this->assertTrue($origin->equals($same));
        $this->assertFalse($origin->equals($similar));
    }

    #[Test]
    public function throwsExceptionOnMismatchCurrencies(): void
    {
        $first = Money::of(1.2, $this->euro);
        $second = Money::of(1.2, $this->usDollar);

        $this->expectException(MismatchCurrencies::class);

        $first->equals($second);
    }

    #[Test]
    public function canBeGreaterThan(): void
    {
        $origin = Money::of(1.2, $this->euro);
        $smaller = Money::of(1.0, $this->euro);
        $greater = Money::of(2.2, $this->euro);

        $this->assertTrue($origin->greaterThan($smaller));
        $this->assertFalse($origin->greaterThan($greater));
    }

    #[Test]
    public function canBeGreaterThanOrEquals(): void
    {
        $origin = Money::of(1.2, $this->euro);
        $smaller = Money::of(1.0, $this->euro);
        $greater = Money::of(2.2, $this->euro);

        $this->assertTrue($origin->greaterThanOrEqualTo($smaller));
        $this->assertTrue($origin->greaterThanOrEqualTo($origin));
        $this->assertFalse($origin->greaterThanOrEqualTo($greater));
    }

    #[Test]
    public function canBeLessThan(): void
    {
        $origin = Money::of(1.2, $this->euro);
        $smaller = Money::of(1.0, $this->euro);
        $greater = Money::of(2.2, $this->euro);

        $this->assertTrue($origin->lessThan($greater));
        $this->assertFalse($origin->lessThan($smaller));
    }

    #[Test]
    public function canBeLessThanOrEquals(): void
    {
        $origin = Money::of(1.2, $this->euro);
        $smaller = Money::of(1.0, $this->euro);
        $greater = Money::of(2.2, $this->euro);

        $this->assertTrue($origin->lessThanOrEqualTo($greater));
        $this->assertTrue($origin->lessThanOrEqualTo($origin));
        $this->assertFalse($origin->lessThanOrEqualTo($smaller));
    }

    #[Test]
    public function canBeEqualToZero(): void
    {
        $negative = Money::ofSub(-1, $this->euro);
        $zero = Money::ofSub(0, $this->euro);
        $positive = Money::ofSub(1, $this->euro);

        $this->assertFalse($negative->equalToZero());
        $this->assertTrue($zero->equalToZero());
        $this->assertFalse($positive->equalToZero());
    }

    #[Test]
    public function canBeGreaterThanZero(): void
    {
        $negative = Money::ofSub(-1, $this->euro);
        $zero = Money::ofSub(0, $this->euro);
        $positive = Money::ofSub(1, $this->euro);

        $this->assertFalse($negative->greaterThanZero());
        $this->assertFalse($zero->greaterThanZero());
        $this->assertTrue($positive->greaterThanZero());
    }

    #[Test]
    public function canBeLessThanZero(): void
    {
        $negative = Money::ofSub(-1, $this->euro);
        $zero = Money::ofSub(0, $this->euro);
        $positive = Money::ofSub(1, $this->euro);

        $this->assertTrue($negative->lessThanZero());
        $this->assertFalse($zero->lessThanZero());
        $this->assertFalse($positive->lessThanZero());
    }

    #[Test]
    public function shouldBeAddedUp(): void
    {
        $amount = 1.2;

        $addend = Money::of($amount, $this->euro);
        $addition = $addend->add($addend);

        $this->assertEquals($amount + $amount, $addition->amount);
    }

    #[Test]
    public function throwsExceptionWhenAddingUpOnMismatchCurrencies(): void
    {
        $augend = Money::of(1.2, $this->euro);
        $addend = Money::of(1.2, $this->usDollar);

        $this->expectException(MismatchCurrencies::class);

        $augend->add($addend);
    }

    #[Test]
    public function shouldBeSubtracted(): void
    {
        $amount = 10.2;

        $minuend = Money::of($amount, $this->euro);
        $subtraction = $minuend->subtract($minuend);

        $this->assertEquals($amount - $amount, $subtraction->amount);
    }

    #[Test]
    public function throwsExceptionWhenSubtractingOnMismatchCurrencies(): void
    {
        $minuend = Money::of(1.2, $this->euro);
        $subtrahend = Money::of(1.2, $this->usDollar);

        $this->expectException(MismatchCurrencies::class);

        $minuend->subtract($subtrahend);
    }

    #[Test]
    public function canBeMultiplied(): void
    {
        $amount = 2.2;

        $multiplier = Money::of($amount, $this->euro);
        $multiplication = $multiplier->multiply($amount);

        $this->assertEquals(round($amount * $amount, 2), $multiplication->amount);
    }

    #[Test]
    public function canBeDevided(): void
    {
        $amount = 2.2;

        $dividend = Money::of($amount, $this->euro);
        $division = $dividend->divide($amount);

        $this->assertEquals(round($amount / $amount, 2), $division->amount);
    }

    #[Test]
    public function shouldBeRoundedToNearestUnit(): void
    {
        $value = 2.35;
        $expected = 2.00;

        $origin = Money::of($value, $this->euro);
        $rounded = $origin->roundToNearest();

        $this->assertEquals($expected, $rounded->amount);
    }

    #[Test]
    public function shouldBeTransformToPositive(): void
    {
        $amount = 1;

        $positive = Money::of($amount, $this->euro);
        $negative = Money::of(-1 * $amount, $this->euro);
        $zero = Money::of(0, $this->euro);

        $absolutePositive = $positive->absolute();
        $absoluteNegative = $negative->absolute();
        $absoluteZero = $zero->absolute();

        $this->assertEquals($amount, $absolutePositive->amount);
        $this->assertEquals($amount, $absoluteNegative->amount);
        $this->assertEquals(0, $absoluteZero->amount);
    }

    #[Test]
    public function shouldBeTransformToNegative(): void
    {
        $amount = -1;

        $positive = Money::of(abs($amount), $this->euro);
        $negative = Money::of($amount, $this->euro);
        $zero = Money::of(0, $this->euro);

        $negatedPositive = $positive->negate();
        $negatedNegative = $negative->negate();
        $negatedZero = $zero->negate();

        $this->assertEquals($amount, $negatedPositive->amount);
        $this->assertEquals($amount, $negatedNegative->amount);
        $this->assertEquals(0, $negatedZero->amount);
    }

    #[Test]
    #[DataProvider('provideAmountsForPositiveString')]
    public function shouldBeReturnAsString(int|float $amount, string $expected): void
    {
        $money = Money::of($amount, $this->euro);

        $this->assertEquals($expected, sprintf('%s', $money));
    }

    public static function provideAmountsForPositiveString(): array
    {
        return [
            [0, '€0.00'],
            [125, '€125.00'],
            [1250.15, '€1250.15'],
            [-0, '€0.00'],
        ];
    }

    #[Test]
    #[DataProvider('provideAmountsForNegativeString')]
    public function shouldBeReturnAsNegativeString(int|float $amount, string $expected): void
    {
        $money = Money::of($amount, $this->euro);

        $this->assertEquals($expected, sprintf('%s', $money));
    }

    public static function provideAmountsForNegativeString(): array
    {
        return [
            [-125, '-€125.00'],
            [-1250.15, '-€1250.15'],
        ];
    }
}
