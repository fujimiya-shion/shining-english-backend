<?php

namespace App\Util\Php;

class PhpUploadLimit
{
    public static function maxKilobytes(): int
    {
        $uploadBytes = self::toBytes(ini_get('upload_max_filesize'));
        $postBytes = self::toBytes(ini_get('post_max_size'));

        $limitBytes = min(
            $uploadBytes > 0 ? $uploadBytes : PHP_INT_MAX,
            $postBytes > 0 ? $postBytes : PHP_INT_MAX
        );

        if ($limitBytes === PHP_INT_MAX || $limitBytes <= 0) {
            return 12288;
        }

        return max(1, (int) floor($limitBytes / 1024));
    }

    private static function toBytes(string|false $value): int
    {
        if (! is_string($value) || $value === '') {
            return 0;
        }

        $normalized = trim($value);
        if ($normalized === '') {
            return 0;
        }

        $unit = strtolower(substr($normalized, -1));
        $number = (float) $normalized;

        return match ($unit) {
            'g' => (int) ($number * 1024 * 1024 * 1024),
            'm' => (int) ($number * 1024 * 1024),
            'k' => (int) ($number * 1024),
            default => (int) $number,
        };
    }
}
