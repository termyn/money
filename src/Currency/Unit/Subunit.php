<?php

declare(strict_types=1);

namespace Termyn\Currency\Unit;

use Webmozart\Assert\Assert;

final readonly class Subunit
{
    public function __construct(
        public string $code,
        public string $symbol,
        public int $fraction,
    ) {
        Assert::notEmpty($this->code);
        Assert::notEmpty($this->symbol);
        Assert::oneOf($this->fraction, [1, 10, 100, 1000]);
    }

    public function equals(self $that): bool
    {
        return $that->code === $this->code
            && $that->symbol === $this->symbol
            && $that->fraction === $this->fraction;
    }

    public function precision(): int
    {
        return intval(log10($this->fraction));
    }
}
