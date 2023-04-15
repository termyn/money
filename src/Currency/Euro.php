<?php

declare(strict_types=1);

namespace Termyn\Currency;

use Termyn\Currency;
use Termyn\Currency\Unit\Subunit;
use Termyn\Currency\Unit\Superunit;

final readonly class Euro extends Currency
{
    public function __construct()
    {
        parent::__construct(
            new Superunit('EUR', '€'),
            new Subunit('cent', 'c', 100),
        );
    }
}
