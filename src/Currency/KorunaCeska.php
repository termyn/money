<?php

declare(strict_types=1);

namespace Termyn\Currency;

use Termyn\Currency;
use Termyn\Currency\Unit\Subunit;
use Termyn\Currency\Unit\Superunit;

final readonly class KorunaCeska extends Currency
{
    public function __construct()
    {
        parent::__construct(
            new Superunit('CZK', 'Kč'),
            new Subunit('haler', 'h', 100),
        );
    }
}
