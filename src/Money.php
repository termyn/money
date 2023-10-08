<?php

declare(strict_types=1);

namespace Termyn;

use Stringable;
use Termyn\Money\MismatchCurrencies;

final readonly class Money implements Stringable
{
    final public const ROUND_HALF_EVEN = PHP_ROUND_HALF_EVEN;

    final public const ROUND_HALF_DOWN = PHP_ROUND_HALF_DOWN;

    final public const ROUND_HALF_ODD = PHP_ROUND_HALF_ODD;

    final public const ROUND_HALF_UP = PHP_ROUND_HALF_UP;

    public float $amount;

    private int $amountInSubunit;

    final private function __construct(
        int|float $amount,
        public Currency $currency,
    ) {
        $fraction = $this->currency->fraction();

        $this->amountInSubunit = intval($amount * $fraction);
        $this->amount = floatval($this->amountInSubunit / $fraction);
    }

    public function __toString(): string
    {
        $sign = $this->lessThanZero() ? '-' : '';

        return sprintf('%s%s%01.2f', $sign, $this->currency->symbol(), $this->absolute()->amount);
    }

    public static function of(
        int|float $amount,
        Currency $currency,
    ): self {
        return new self($amount, $currency);
    }

    public static function ofSub(
        int $amount,
        Currency $currency,
    ): self {
        return new self($amount / $currency->fraction(), $currency);
    }

    public static function from(string $money): self
    {
        $pattern = '/^(\-|\+)?([^0-9\-\+]{1,3})([0-9]+(\.[0-9]+)?)$/';
        $parts = [];

        $matched = (bool) preg_match($pattern, $money, $parts);
        if (! $matched) {
            throw new InvalidMoneyString($money);
        }

        $symbol = sprintf('%s', $parts[2]);
        $amount = floatval(
            sprintf('%s%s', $parts[1], $parts[3])
        );

        return self::of($amount, Currencies::fromSymbol($symbol));
    }

    public function isComparable(self $that): bool
    {
        return $this->currency->equals($that->currency);
    }

    public function equals(self $that): bool
    {
        return $this->compare($that) === 0;
    }

    public function greaterThan(self $that): bool
    {
        return $this->compare($that) > 0;
    }

    public function greaterThanOrEqualTo(self $that): bool
    {
        return $this->compare($that) >= 0;
    }

    public function lessThan(self $that): bool
    {
        return $this->compare($that) < 0;
    }

    public function lessThanOrEqualTo(self $that): bool
    {
        return $this->compare($that) <= 0;
    }

    public function equalToZero(): bool
    {
        return $this->amountInSubunit === 0;
    }

    public function greaterThanZero(): bool
    {
        return $this->amountInSubunit > 0;
    }

    public function lessThanZero(): bool
    {
        return $this->amountInSubunit < 0;
    }

    public function add(self $that): self
    {
        if (! $this->isComparable($that)) {
            throw new MismatchCurrencies($this, $that);
        }

        $amountInSubunit = $this->amountInSubunit + $that->amountInSubunit;

        return self::ofSub($amountInSubunit, $this->currency);
    }

    public function subtract(self $that): self
    {
        if (! $this->isComparable($that)) {
            throw new MismatchCurrencies($this, $that);
        }

        $amountInSubunit = $this->amountInSubunit - $that->amountInSubunit;

        return self::ofSub(
            amount: $amountInSubunit,
            currency: $this->currency
        );
    }

    /**
     * @phpstan-param self::ROUND_* $rounding
     */
    public function multiply(
        int|float $factor,
        int $precision = 2,
        int $rounding = self::ROUND_HALF_UP,
    ): self {
        $amountInSubunit = round(
            num: $this->amountInSubunit * $factor,
            precision: $precision,
            mode: $rounding,
        );

        return self::ofSub(intval($amountInSubunit), $this->currency);
    }

    /**
     * @phpstan-param self::ROUND_* $rounding
     */
    public function divide(
        int|float $divisor,
        int $rounding = self::ROUND_HALF_UP,
    ): self {
        $amountInSubunit = round(
            num: $this->amountInSubunit / $divisor,
            mode: $rounding,
        );

        return self::ofSub(intval($amountInSubunit), $this->currency);
    }

    /**
     * @phpstan-param self::ROUND_* $rounding
     */
    public function roundToNearest(
        int $rounding = self::ROUND_HALF_UP,
    ): self {
        return self::of(
            round($this->amount, 0, $rounding),
            $this->currency,
        );
    }

    public function absolute(): self
    {
        return self::ofSub(
            amount: abs($this->amountInSubunit),
            currency: $this->currency,
        );
    }

    public function negate(): self
    {
        return self::ofSub(
            amount: $this->greaterThanZero() ? -1 * $this->amountInSubunit : $this->amountInSubunit,
            currency: $this->currency,
        );
    }

    private function compare(self $that): int
    {
        if (! $this->isComparable($that)) {
            throw new MismatchCurrencies($this, $that);
        }

        return $this->amountInSubunit <=> $that->amountInSubunit;
    }
}
