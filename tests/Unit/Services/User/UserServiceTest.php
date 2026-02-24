<?php

namespace Tests\Unit\Services\User;

use App\DTO\User\Auth\LoginResponse;
use App\DTO\User\Auth\RegisterResponse;
use App\Models\User;
use App\Repositories\User\IUserDeviceRepository;
use App\Repositories\User\IUserRepository;
use App\Services\User\IUserService;
use App\Services\User\UserService;
use App\ValueObjects\DeviceInfo;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Mockery;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

it('implements shared service contract', function (): void {
    $userRepository = Mockery::mock(IUserRepository::class);
    $deviceRepository = Mockery::mock(IUserDeviceRepository::class);
    $service = new UserService($userRepository, $deviceRepository);

    assertServiceContract($service, IUserService::class, $userRepository);
});

it('registers user and returns token', function (): void {
    $user = new class extends User {
        public function createToken(string $name, array $abilities = ['*'], ?\DateTimeInterface $expiresAt = null): object
        {
            return (object) ['plainTextToken' => 'token-123'];
        }
    };
    $user->id = 1;

    $userRepository = Mockery::mock(IUserRepository::class);
    $userRepository->shouldReceive('create')
        ->once()
        ->with([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0900000000',
            'password' => 'secret',
        ])
        ->andReturn($user);

    $deviceRepository = Mockery::mock(IUserDeviceRepository::class);

    $service = new UserService($userRepository, $deviceRepository);
    $result = $service->register('Test User', 'test@example.com', '0900000000', 'secret');

    expect($result)->toBeInstanceOf(RegisterResponse::class);
    expect($result->token)->toBe('token-123');
});

it('logs in user and records device', function (): void {
    $user = Mockery::mock(User::class)->makePartial();
    $user->password = Hash::make('secret');
    $user->id = 10;
    $user->shouldReceive('createToken')
        ->once()
        ->with('user_auth_token')
        ->andReturn((object) [
            'plainTextToken' => 'token-xyz',
            'accessToken' => (object) ['id' => 55],
        ]);

    $userRepository = Mockery::mock(IUserRepository::class);
    $userRepository->shouldReceive('getBy')
        ->once()
        ->with(['email' => 'test@example.com'])
        ->andReturn(new EloquentCollection([$user]));

    $deviceRepository = Mockery::mock(IUserDeviceRepository::class);
    $deviceRepository->shouldReceive('create')
        ->once()
        ->with(Mockery::on(function (array $data): bool {
            return $data['user_id'] === 10
                && $data['personal_access_token_id'] === 55
                && $data['device_identifier'] === 'device-1';
        }));

    $service = new UserService($userRepository, $deviceRepository);
    $device = new DeviceInfo('device-1', 'iPhone', 'ios', '127.0.0.1', 'agent');

    $result = $service->login('test@example.com', 'secret', $device);

    expect($result)->toBeInstanceOf(LoginResponse::class);
    expect($result->token)->toBe('token-xyz');
});

it('throws when credentials are invalid', function (): void {
    $user = Mockery::mock(User::class)->makePartial();
    $user->password = Hash::make('secret');

    $userRepository = Mockery::mock(IUserRepository::class);
    $userRepository->shouldReceive('getBy')
        ->once()
        ->with(['email' => 'test@example.com'])
        ->andReturn(new EloquentCollection([$user]));

    $deviceRepository = Mockery::mock(IUserDeviceRepository::class);

    $service = new UserService($userRepository, $deviceRepository);
    $device = new DeviceInfo('device-1');

    expect(fn () => $service->login('test@example.com', 'wrong', $device))
        ->toThrow(Exception::class, 'Invalid credentials');
});

it('logs out by token', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('logout')->plainTextToken;
    $accessToken = PersonalAccessToken::findToken($token);

    $userRepository = Mockery::mock(IUserRepository::class);
    $deviceRepository = Mockery::mock(IUserDeviceRepository::class);
    $deviceRepository->shouldReceive('markLoggedOutByTokenId')
        ->once()
        ->with($accessToken->id)
        ->andReturn(1);

    $service = new UserService($userRepository, $deviceRepository);

    expect($service->logoutByToken($token))->toBeTrue();
    expect(PersonalAccessToken::findToken($token))->toBeNull();
});

it('returns false when token is invalid', function (): void {
    $userRepository = Mockery::mock(IUserRepository::class);
    $deviceRepository = Mockery::mock(IUserDeviceRepository::class);
    $deviceRepository->shouldReceive('markLoggedOutByTokenId')->never();

    $service = new UserService($userRepository, $deviceRepository);

    expect($service->logoutByToken('invalid-token'))->toBeFalse();
});
