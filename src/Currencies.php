<?php

declare(strict_types=1);

namespace Termyn;

use Termyn\Currency\Euro;
use Termyn\Currency\KorunaCeska;
use Termyn\Currency\UnsupportedCurrency;
use Termyn\Currency\UsDollar;

final class Currencies
{
    private static array $codes = [
        'CZK' => KorunaCeska::class,
        'EUR' => Euro::class,
        'USD' => UsDollar::class,
    ];

    private static array $symbols = [
        'Kč' => KorunaCeska::class,
        '€' => Euro::class,
        '$' => UsDollar::class,
    ];

    public static function fromCode(string $code): Currency
    {
        $class = self::$codes[mb_strtoupper($code)] ?? null;

        return is_a($class, Currency::class, true)
            ? new $class()
            : throw UnsupportedCurrency::code($code);
    }

    public static function fromSymbol(string $symbol): Currency
    {
        $class = self::$symbols[$symbol] ?? null;

        return is_a($class, Currency::class, true)
            ? new $class()
            : throw UnsupportedCurrency::symbol($symbol);
    }
}
