<?php

declare(strict_types=1);

namespace Termyn\Currency;

use DomainException;
use Termyn\Currency;

final class UnsupportedCurrency extends DomainException
{
    public function __construct(string $code)
    {
        parent::__construct(
            message: sprintf('Currency "%s" is currently not supported. Add it by implementing the interface %s', $code, Currency::class),
            code: 501,
        );
    }
}
