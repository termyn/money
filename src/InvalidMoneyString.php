<?php

declare(strict_types=1);

namespace Termyn;

use InvalidArgumentException;

final class InvalidMoneyString extends InvalidArgumentException
{
    public function __construct(string $money)
    {
        parent::__construct(
            sprintf('Money string "%s" is invalid. Valid string should be: $1500.00 | -$1250.15 | +$800.15', $money)
        );
    }
}
