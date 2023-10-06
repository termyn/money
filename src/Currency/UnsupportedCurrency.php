<?php

declare(strict_types=1);

namespace Termyn\Currency;

use DomainException;

final class UnsupportedCurrency extends DomainException
{
    public static function code(string $code): self
    {
        return new self(
            message: sprintf('Currency code "%s" is currently not supported.', $code),
            code: 501,
        );
    }

    public static function symbol(string $symbol): self
    {
        return new self(
            message: sprintf('Currency symbol "%s" is currently not supported.', $symbol),
            code: 501,
        );
    }
}
