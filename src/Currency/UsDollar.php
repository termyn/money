<?php

declare(strict_types=1);

namespace Termyn\Currency;

use Termyn\Currency;
use Termyn\Currency\Unit\Subunit;
use Termyn\Currency\Unit\Superunit;

final class UsDollar extends Currency
{
    public function __construct()
    {
        parent::__construct(
            new Superunit('USD', '$'),
            new Subunit('cent', 'c', 100),
        );
    }
}
