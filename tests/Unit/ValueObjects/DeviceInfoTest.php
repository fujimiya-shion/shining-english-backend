<?php

use App\ValueObjects\DeviceInfo;

it('builds device info from array', function (): void {
    $device = DeviceInfo::fromArray([
        'device_identifier' => 'device-1',
        'device_name' => 'iPhone',
        'platform' => 'ios',
        'ip_address' => '127.0.0.1',
        'user_agent' => 'UA',
    ]);

    expect($device->identifier)->toBe('device-1');
    expect($device->name)->toBe('iPhone');
    expect($device->platform)->toBe('ios');
    expect($device->ipAddress)->toBe('127.0.0.1');
    expect($device->userAgent)->toBe('UA');
});

it('throws when device identifier is missing', function (): void {
    expect(fn () => DeviceInfo::fromArray([]))
        ->toThrow(InvalidArgumentException::class, 'device_identifier is required');
});
