<?php

declare(strict_types=1);

namespace Termyn;

use Stringable;
use Termyn\Currency\Unit\Subunit;
use Termyn\Currency\Unit\Superunit;

abstract readonly class Currency implements Stringable
{
    public function __construct(
        public Superunit $mainUnit,
        public Subunit $subunit,
    ) {
    }

    public function __toString(): string
    {
        return $this->code();
    }

    public function equals(self $that): bool
    {
        return $this->mainUnit->equals($that->mainUnit)
            && $this->subunit->equals($that->subunit);
    }

    public function code(): string
    {
        return $this->mainUnit->code;
    }

    public function fraction(): int
    {
        return $this->subunit->fraction;
    }

    public function precision(): int
    {
        return $this->subunit->precision();
    }
}
