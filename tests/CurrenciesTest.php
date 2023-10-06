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
        $codes = ['CZK', 'EUR', 'USD'];

        foreach ($codes as $code) {
            $currency = Currencies::fromCode($code);

            $this->assertEquals($code, $currency->code());
        }
    }

    #[Test]
    public function shouldBeCreatedFromSymbol(): void
    {
        $symbols = ['Kč', '€', '$'];

        foreach ($symbols as $symbol) {
            $currency = Currencies::fromSymbol($symbol);

            $this->assertEquals($symbol, $currency->mainUnit->symbol);
        }
    }

    #[Test]
    public function throwsExceptionIfCurrencyCodeIsUnsupported(): void
    {
        $this->expectException(UnsupportedCurrency::class);

        Currencies::fromSymbol('Sk');
    }
}
