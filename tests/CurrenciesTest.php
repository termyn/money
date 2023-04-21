<?php

declare(strict_types=1);

namespace Termyn\Test;

use PHPUnit\Framework\TestCase;
use Termyn\Currencies;
use Termyn\Currency\UnsupportedCurrency;

final class CurrenciesTest extends TestCase
{
    public function testCreationOfCurrencyFromCode(): void
    {
        $codes = ['EUR', 'USD'];

        foreach ($codes as $code) {
            $currency = Currencies::from($code);

            $this->assertEquals($code, $currency->code());
        }
    }

    public function testThrowsUnsupportedCurrencyException(): void
    {
        $this->expectException(UnsupportedCurrency::class);

        Currencies::from('CZK');
    }
}
