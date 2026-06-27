<?php

declare(strict_types=1);

namespace App\Integrations\Payments\Support;

class PayosSignature
{
    /**
     * @param  array<string, scalar|null>  $data
     */
    public static function sign(array $data, string $checksumKey): string
    {
        ksort($data);

        $segments = [];
        foreach ($data as $key => $value) {
            $segments[] = $key.'='.self::normalize($value);
        }

        return hash_hmac('sha256', implode('&', $segments), $checksumKey);
    }

    /**
     * @param  array<string, scalar|null>  $data
     */
    public static function verify(array $data, string $signature, string $checksumKey): bool
    {
        return hash_equals(
            strtolower($signature),
            strtolower(self::sign($data, $checksumKey)),
        );
    }

    private static function normalize(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string) $value;
    }
}
