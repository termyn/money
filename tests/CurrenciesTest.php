<?php

declare(strict_types=1);

namespace Termyn\Test;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Termyn\Currencies;
use Termyn\Currency\UnsupportedCurrency;

final class CurrenciesTest extends TestCase
{
    #[Test]
    public function shouldBeCreatedFromCode(): void
    {
        $codes = ['EUR', 'USD'];

        foreach ($codes as $code) {
            $currency = Currencies::from($code);

            $this->assertEquals($code, $currency->code());
        }
    }

    #[Test]
    public function throwsExceptionIfCurrencyIsUnsupported(): void
    {
        $this->expectException(UnsupportedCurrency::class);

        Currencies::from('CZK');
    }
}
