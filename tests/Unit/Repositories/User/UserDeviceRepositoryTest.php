<?php

use App\Models\User;
use App\Models\UserDevice;
use App\Repositories\User\UserDeviceRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(TestCase::class);

it('marks a device as logged out by token id', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('logout')->plainTextToken;
    $accessToken = PersonalAccessToken::findToken($token);

    $device = UserDevice::query()->create([
        'user_id' => $user->id,
        'personal_access_token_id' => $accessToken->id,
        'device_identifier' => 'device-1',
        'device_name' => 'iPhone',
        'platform' => 'ios',
        'ip_address' => '127.0.0.1',
        'user_agent' => 'test-agent',
        'logged_in_at' => now()->subDay(),
        'last_seen_at' => now()->subDay(),
    ]);

    $repository = new UserDeviceRepository(new UserDevice);
    $updated = $repository->markLoggedOutByTokenId($accessToken->id);

    expect($updated)->toBe(1);
    $device->refresh();
    expect($device->logged_out_at)->not->toBeNull();
    expect($device->last_seen_at)->not->toBeNull();
});
