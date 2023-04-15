<?php

declare(strict_types=1);

namespace Termyn;

use Termyn\Currency\Euro;
use Termyn\Currency\UnsupportedCurrency;
use Termyn\Currency\UsDollar;

final class Currencies
{
    private static array $map = [
        'EUR' => Euro::class,
        'USD' => UsDollar::class,
    ];

    public static function from(string $code): Currency
    {
        return new self::$map[mb_strtolower($code)]() ?? throw new UnsupportedCurrency($code);
    }
}