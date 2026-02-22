<?php

use App\Models\User;
use App\Services\User\IUserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

uses(RefreshDatabase::class);

it('updates current user profile', function (): void {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'phone' => '0900000000',
    ]);
    $token = $user->createToken('user-update')->plainTextToken;

    $payload = [
        'name' => 'New Name',
        'phone' => '0911111111',
        'birthday' => '1995-05-10',
        'avatar' => 'https://example.com/avatar.png',
    ];

    $response = $this->postJson('/api/v1/user/update', $payload, [
        'User-Authorization' => $token,
    ]);

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'message' => 'Updated',
    ]);

    $user->refresh();
    expect($user->name)->toBe('New Name');
    expect($user->phone)->toBe('0911111111');
    expect($user->birthday->format('Y-m-d'))->toBe('1995-05-10');
});

it('validates user update payload', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('user-update')->plainTextToken;

    $response = $this->postJson('/api/v1/user/update', [
        'birthday' => 'invalid-date',
        'password' => '123',
    ], [
        'User-Authorization' => $token,
    ]);

    $response->assertStatus(422);
    $response->assertJsonPath('errors.birthday.0', 'Birthday must be a valid date.');
    $response->assertJsonPath('errors.password.0', 'Password must be at least 6 characters.');
});

it('returns unauthenticated when token is missing', function (): void {
    $response = $this->postJson('/api/v1/user/update', [
        'name' => 'No Auth',
    ]);

    $response->assertStatus(401);
    $response->assertJsonFragment([
        'message' => 'Unauthenticated',
    ]);
});

it('returns not found when service cannot find user', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('user-update')->plainTextToken;

    $service = Mockery::mock(IUserService::class);
    $service->shouldReceive('update')
        ->once()
        ->with($user->id, Mockery::type('array'))
        ->andThrow(new ModelNotFoundException('User not found'));
    app()->instance(IUserService::class, $service);

    $response = $this->postJson('/api/v1/user/update', [
        'name' => 'Missing User',
    ], [
        'User-Authorization' => $token,
    ]);

    $response->assertStatus(404);
    $response->assertJsonFragment([
        'message' => 'User not found',
    ]);
});

it('returns error when update fails', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('user-update')->plainTextToken;

    $service = Mockery::mock(IUserService::class);
    $service->shouldReceive('update')
        ->once()
        ->with($user->id, Mockery::type('array'))
        ->andThrow(new Exception('Update failed'));
    app()->instance(IUserService::class, $service);

    $response = $this->postJson('/api/v1/user/update', [
        'name' => 'Any Name',
    ], [
        'User-Authorization' => $token,
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment([
        'message' => 'Update failed',
    ]);
});

it('rejects null for required user fields', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('user-update')->plainTextToken;

    $response = $this->postJson('/api/v1/user/update', [
        'name' => null,
        'phone' => null,
    ], [
        'User-Authorization' => $token,
    ]);

    $response->assertStatus(422);
    $response->assertJsonPath('errors.name.0', 'Name is required.');
    $response->assertJsonPath('errors.phone.0', 'Phone is required.');
});
