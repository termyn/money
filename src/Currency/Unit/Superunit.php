<?php

declare(strict_types=1);

namespace Termyn\Currency\Unit;

use Webmozart\Assert\Assert;

final readonly class Superunit
{
    public string $code;

    public function __construct(
        string $code,
        public string $symbol,
    ) {
        $this->code = mb_strtoupper($code);

        Assert::regex($this->code, '/^[A-Z]{3}$/');
        Assert::notEmpty($this->symbol);
    }

    public function equals(self $that): bool
    {
        return $that->code === $this->code
            && $that->symbol === $this->symbol;
    }
}
