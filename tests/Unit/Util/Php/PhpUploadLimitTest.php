<?php

namespace App\Util\Php {
    final class PhpUploadLimitIniState
    {
        /** @var array<string, string|false> */
        public static array $values = [];
    }

    if (! function_exists(__NAMESPACE__.'\\ini_get')) {
        function ini_get(string $option): string|false
        {
            return PhpUploadLimitIniState::$values[$option] ?? false;
        }
    }
}

namespace {
    use App\Util\Php\PhpUploadLimit;
    use App\Util\Php\PhpUploadLimitIniState;

    test('php upload limit returns fallback when php ini values are invalid', function (): void {
        PhpUploadLimitIniState::$values = [
            'upload_max_filesize' => false,
            'post_max_size' => false,
        ];

        expect(PhpUploadLimit::maxKilobytes())->toBe(12288);
    });

    test('php upload limit uses minimum between upload and post size', function (): void {
        PhpUploadLimitIniState::$values = [
            'upload_max_filesize' => '100M',
            'post_max_size' => '50M',
        ];

        expect(PhpUploadLimit::maxKilobytes())->toBe(51200);
    });

    test('php upload limit parses g m k and raw byte values', function (): void {
        expect(PhpUploadLimit::maxKilobytesFromIniValues('1G', '2G'))->toBe(1048576);
        expect(PhpUploadLimit::maxKilobytesFromIniValues('10M', '20M'))->toBe(10240);
        expect(PhpUploadLimit::maxKilobytesFromIniValues('2048K', '4M'))->toBe(2048);
        expect(PhpUploadLimit::maxKilobytesFromIniValues('2048', '4096'))->toBe(2);
    });

    test('php upload limit handles empty and whitespace values', function (): void {
        expect(PhpUploadLimit::maxKilobytesFromIniValues('', '   '))->toBe(12288);
    });
}
