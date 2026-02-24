<?php

namespace App\Enums;

enum StarTransactionType: string
{
    case Increase = 'increase';
    case Decrease = 'decrease';

    public static function values(): array
    {
        return array_map(
            static fn (self $case): string => $case->value,
            self::cases()
        );
    }
}
