<?php

declare(strict_types=1);

namespace Termyn\Money;

use DomainException;
use Termyn\Money;

final class MismatchCurrencies extends DomainException
{
    public function __construct(Money $origin, Money $another)
    {
        parent::__construct(
            message: sprintf('Mathematical operations are allowed for only the same currency (%s => %s).', $origin->currency, $another->currency),
            code: 405,
        );
    }
}
