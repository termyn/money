<?php

declare(strict_types=1);

namespace Termyn\Test;

use PHPUnit\Framework\TestCase;
use Termyn\Currency\Euro;
use Termyn\Currency\UsDollar;
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

    public function testCreationBasedOnSuperunit(): void
    {
        $amount = 1.20;

        $money = Money::of($amount, $this->euro);

        $this->assertEquals($amount, $money->amount);
        $this->assertEquals($this->euro, $money->currency);
    }

    public function testCreationBasedOnSubunit(): void
    {
        $amount = 1.20;

        $money = Money::ofSub(intval($amount * 100), $this->euro);

        $this->assertEquals($amount, $money->amount);
        $this->assertEquals($this->euro, $money->currency);
    }

    public function testComparison(): void
    {
        $origin = Money::of(1.2, $this->euro);

        $comparable = Money::of(2.2, $this->euro);
        $notComparable = Money::of(1.2, $this->usDollar);

        $this->assertTrue($origin->isComparable($comparable));
        $this->assertFalse($origin->isComparable($notComparable));
    }

    public function testThrowExceptionOnMismatchCurrencies(): void
    {
        $first = Money::of(1.2, $this->euro);
        $second = Money::of(1.2, $this->usDollar);

        $this->expectException(MismatchCurrencies::class);

        $first->equals($second);
        $first->add($second);
        $first->subtract($second);
    }

    public function testEquality(): void
    {
        $origin = Money::of(1.2, $this->euro);
        $same = Money::of(1.2, $this->euro);
        $similar = Money::of(2.2, $this->euro);

        $this->assertTrue($origin->equals($same));
        $this->assertFalse($origin->equals($similar));
    }

    public function testItIsGreaterThan(): void
    {
        $origin = Money::of(1.2, $this->euro);
        $smaller = Money::of(1.0, $this->euro);
        $greater = Money::of(2.2, $this->euro);

        $this->assertTrue($origin->greaterThan($smaller));
        $this->assertFalse($origin->greaterThan($greater));
    }

    public function testItIsGreaterThanOrEquals(): void
    {
        $origin = Money::of(1.2, $this->euro);
        $smaller = Money::of(1.0, $this->euro);
        $greater = Money::of(2.2, $this->euro);

        $this->assertTrue($origin->greaterThanOrEqualTo($smaller));
        $this->assertTrue($origin->greaterThanOrEqualTo($origin));
        $this->assertFalse($origin->greaterThanOrEqualTo($greater));
    }

    public function testItIsLessThan(): void
    {
        $origin = Money::of(1.2, $this->euro);
        $smaller = Money::of(1.0, $this->euro);
        $greater = Money::of(2.2, $this->euro);

        $this->assertTrue($origin->lessThan($greater));
        $this->assertFalse($origin->lessThan($smaller));
    }

    public function testItIsLessThanOrEquals(): void
    {
        $origin = Money::of(1.2, $this->euro);
        $smaller = Money::of(1.0, $this->euro);
        $greater = Money::of(2.2, $this->euro);

        $this->assertTrue($origin->lessThanOrEqualTo($greater));
        $this->assertTrue($origin->lessThanOrEqualTo($origin));
        $this->assertFalse($origin->lessThanOrEqualTo($smaller));
    }

    public function testItEqualsToZero(): void
    {
        $negative = Money::ofSub(-1, $this->euro);
        $zero = Money::ofSub(0, $this->euro);
        $positive = Money::ofSub(1, $this->euro);

        $this->assertFalse($negative->equalToZero());
        $this->assertTrue($zero->equalToZero());
        $this->assertFalse($positive->equalToZero());
    }

    public function testItIsGreaterThanZero(): void
    {
        $negative = Money::ofSub(-1, $this->euro);
        $zero = Money::ofSub(0, $this->euro);
        $positive = Money::ofSub(1, $this->euro);

        $this->assertFalse($negative->greaterThanZero());
        $this->assertFalse($zero->greaterThanZero());
        $this->assertTrue($positive->greaterThanZero());
    }

    public function testItIsLessThanZero(): void
    {
        $negative = Money::ofSub(-1, $this->euro);
        $zero = Money::ofSub(0, $this->euro);
        $positive = Money::ofSub(1, $this->euro);

        $this->assertTrue($negative->lessThanZero());
        $this->assertFalse($zero->lessThanZero());
        $this->assertFalse($positive->lessThanZero());
    }

    public function testAddition(): void
    {
        $amount = 1.2;

        $addend = Money::of($amount, $this->euro);
        $addition = $addend->add($addend);

        $this->assertEquals($amount + $amount, $addition->amount);
    }

    public function testSubtraction(): void
    {
        $amount = 10.2;

        $minuend = Money::of($amount, $this->euro);
        $subtraction = $minuend->subtract($minuend);

        $this->assertEquals($amount - $amount, $subtraction->amount);
    }

    public function testMultiplication(): void
    {
        $amount = 2.2;

        $multiplier = Money::of($amount, $this->euro);
        $multiplication = $multiplier->multiply($amount);

        $this->assertEquals(round($amount * $amount, 2), $multiplication->amount);
    }

    public function testDivision(): void
    {
        $amount = 2.2;

        $dividend = Money::of($amount, $this->euro);
        $division = $dividend->divide($amount);

        $this->assertEquals(round($amount / $amount, 2), $division->amount);
    }

    public function testItTransformToPositive(): void
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

    public function testItTransformToNegative(): void
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

    public function testToString(): void
    {
        $money = Money::of(1.2, $this->euro);

        $this->assertEquals('1.20 EUR', sprintf('%s', $money));
    }
}
