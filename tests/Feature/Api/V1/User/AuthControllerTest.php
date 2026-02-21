<?php

use App\Models\User;
use App\Models\UserDevice;
use App\Services\User\IUserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('registers a user', function (): void {
    $email = 'newuser-'.Str::random(8).'@example.com';

    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'New User',
        'email' => $email,
        'phone' => '0900000000',
        'password' => 'secret123',
    ]);

    $response->assertStatus(201);
    $response->assertJsonFragment([
        'message' => 'Register successfully',
    ]);

    $user = User::query()->where('email', $email)->first();
    expect($user)->not->toBeNull();
});

it('returns error when register service throws', function (): void {
    $service = Mockery::mock(IUserService::class);
    $service->shouldReceive('register')
        ->once()
        ->andThrow(new Exception('Register failed'));
    app()->instance(IUserService::class, $service);

    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'New User',
        'email' => 'newuser-'.Str::random(8).'@example.com',
        'phone' => '0900000000',
        'password' => 'secret123',
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment([
        'message' => 'Register failed',
    ]);
});

it('validates register request', function (): void {
    $response = $this->postJson('/api/v1/auth/register', [
        'password' => 'secret123',
    ]);

    $response->assertStatus(422);
    $response->assertJsonPath('errors.name.0', 'Name is required.');
    $response->assertJsonPath('errors.email.0', 'Email is required.');
    $response->assertJsonPath('errors.phone.0', 'Phone is required.');
});

it('logs in a user', function (): void {
    $user = User::factory()->create([
        'email' => 'login@example.com',
        'password' => 'password',
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'login@example.com',
        'password' => 'password',
        'device_identifier' => 'device-1',
        'device_name' => 'iPhone',
        'platform' => 'ios',
    ]);

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'message' => 'Login successfully',
    ]);

    $device = UserDevice::query()->where('user_id', $user->id)->first();
    expect($device)->not->toBeNull();
    expect($device->device_identifier)->toBe('device-1');
});

it('rejects invalid credentials', function (): void {
    User::factory()->create([
        'email' => 'login@example.com',
        'password' => 'password',
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'login@example.com',
        'password' => 'wrong',
        'device_identifier' => 'device-1',
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment([
        'message' => 'Invalid credentials',
    ]);
});

it('validates login request', function (): void {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'login@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(422);
    $response->assertJsonPath('errors.device_identifier.0', 'Device identifier is required.');
});

it('returns current user profile', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('auth')->plainTextToken;

    $response = $this->getJson('/api/v1/auth/me', [
        'User-Authorization' => $token,
    ]);

    $response->assertStatus(200);
    $response->assertJsonPath('data.id', $user->id);
});

it('logs out current user', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('auth')->plainTextToken;
    $accessToken = PersonalAccessToken::findToken($token);

    $device = UserDevice::query()->create([
        'user_id' => $user->id,
        'personal_access_token_id' => $accessToken->id,
        'device_identifier' => 'device-logout',
        'device_name' => 'Pixel',
        'platform' => 'android',
        'ip_address' => '127.0.0.1',
        'user_agent' => 'test-agent',
        'logged_in_at' => now()->subDay(),
        'last_seen_at' => now()->subDay(),
    ]);

    $response = $this->postJson('/api/v1/auth/logout', [], [
        'User-Authorization' => $token,
    ]);

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'message' => 'Logged out',
    ]);

    $device->refresh();
    expect($device->logged_out_at)->not->toBeNull();
    expect(PersonalAccessToken::findToken($token))->toBeNull();
});

it('returns unauthenticated when logout token is missing', function (): void {
    $this->withoutMiddleware();

    $response = $this->postJson('/api/v1/auth/logout');

    $response->assertStatus(401);
    $response->assertJsonFragment([
        'message' => 'Unauthenticated',
    ]);
});

it('returns unauthenticated when logout service rejects token', function (): void {
    $this->withoutMiddleware();

    $service = Mockery::mock(IUserService::class);
    $service->shouldReceive('logoutByToken')
        ->once()
        ->with('invalid-token')
        ->andReturn(false);
    app()->instance(IUserService::class, $service);

    $response = $this->postJson('/api/v1/auth/logout', [], [
        'User-Authorization' => 'invalid-token',
    ]);

    $response->assertStatus(401);
    $response->assertJsonFragment([
        'message' => 'Unauthenticated',
    ]);
});
