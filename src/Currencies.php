<?php

declare(strict_types=1);

namespace Termyn;

use Termyn\Currency\Euro;
use Termyn\Currency\KorunaCeska;
use Termyn\Currency\UnsupportedCurrency;
use Termyn\Currency\UsDollar;

final class Currencies
{
    private static array $map = [
        'CZK' => KorunaCeska::class,
        'EUR' => Euro::class,
        'USD' => UsDollar::class,
    ];

    public static function from(string $code): Currency
    {
        $classMap = self::$map;
        $class = $classMap[mb_strtoupper($code)] ?? null;

        return is_a($class, Currency::class, true) ? new $class() : throw new UnsupportedCurrency($code);
    }
}
