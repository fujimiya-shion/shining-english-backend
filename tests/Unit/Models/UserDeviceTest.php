<?php

use App\Models\UserDevice;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

it('defines fillable attributes', function (): void {
    $device = new UserDevice;

    expect($device->getFillable())->toEqual([
        'user_id',
        'personal_access_token_id',
        'device_identifier',
        'device_name',
        'platform',
        'ip_address',
        'user_agent',
        'logged_in_at',
        'last_seen_at',
        'logged_out_at',
    ]);
});

it('casts attributes correctly', function (): void {
    $device = new UserDevice;

    expect($device->getCasts())->toMatchArray([
        'logged_in_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'logged_out_at' => 'datetime',
    ]);
});

it('defines user relation', function (): void {
    $method = new ReflectionMethod(UserDevice::class, 'user');

    expect($method->getReturnType()?->getName())->toBe(BelongsTo::class);
});

it('defines personal access token relation', function (): void {
    $method = new ReflectionMethod(UserDevice::class, 'personalAccessToken');

    expect($method->getReturnType()?->getName())->toBe(BelongsTo::class);
});
