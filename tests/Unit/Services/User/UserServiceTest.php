<?php

namespace Tests\Unit\Services\User;

use App\DTO\User\Auth\LoginResponse;
use App\DTO\User\Auth\RegisterResponse;
use App\Jobs\InitUserStarJob;
use App\Jobs\SendEmailVerificationJob;
use App\Models\User;
use App\Repositories\User\IUserDeviceRepository;
use App\Repositories\User\IUserRepository;
use App\Services\User\IUserService;
use App\Services\User\UserService;
use App\ValueObjects\DeviceInfo;
use Closure;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Bus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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

it('registers user and dispatches verification + init jobs', function (): void {
    $user = new User;
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

    Bus::fake();

    $service = new UserService($userRepository, $deviceRepository);
    $result = $service->register('Test User', 'test@example.com', '0900000000', 'secret');

    expect($result)->toBeInstanceOf(RegisterResponse::class);
    expect($result->user)->toBe($user);
    Bus::assertDispatched(InitUserStarJob::class);
    Bus::assertDispatched(SendEmailVerificationJob::class);
});

it('logs in user and records device', function (): void {
    $user = Mockery::mock(User::class)->makePartial();
    $user->password = Hash::make('secret');
    $user->id = 10;
    $user->shouldReceive('hasVerifiedEmail')->once()->andReturnTrue();
    $user->shouldReceive('createToken')
        ->once()
        ->with('user_auth_token')
        ->andReturn((object) [
            'plainTextToken' => 'token-xyz',
            'accessToken' => (object) ['id' => 55],
        ]);

    $userRepository = Mockery::mock(IUserRepository::class);
    $userRepository->shouldReceive('findByEmail')
        ->once()
        ->with('test@example.com')
        ->andReturn($user);

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
    $userRepository->shouldReceive('findByEmail')
        ->once()
        ->with('test@example.com')
        ->andReturn($user);

    $deviceRepository = Mockery::mock(IUserDeviceRepository::class);

    $service = new UserService($userRepository, $deviceRepository);
    $device = new DeviceInfo('device-1');

    expect(fn () => $service->login('test@example.com', 'wrong', $device))
        ->toThrow(Exception::class, 'Invalid credentials');
});

it('throws when email is not verified', function (): void {
    $user = Mockery::mock(User::class)->makePartial();
    $user->password = Hash::make('secret');
    $user->shouldReceive('hasVerifiedEmail')->once()->andReturnFalse();

    $userRepository = Mockery::mock(IUserRepository::class);
    $userRepository->shouldReceive('findByEmail')
        ->once()
        ->with('test@example.com')
        ->andReturn($user);

    $deviceRepository = Mockery::mock(IUserDeviceRepository::class);

    $service = new UserService($userRepository, $deviceRepository);
    $device = new DeviceInfo('device-1');

    expect(fn () => $service->login('test@example.com', 'secret', $device))
        ->toThrow(Exception::class, 'Email is not verified.');
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

it('sends a password reset link through the broker', function (): void {
    Password::shouldReceive('broker')
        ->once()
        ->with('users')
        ->andReturnSelf();
    Password::shouldReceive('sendResetLink')
        ->once()
        ->with(['email' => 'test@example.com'])
        ->andReturn(Password::RESET_LINK_SENT);

    $userRepository = Mockery::mock(IUserRepository::class);
    $deviceRepository = Mockery::mock(IUserDeviceRepository::class);
    $service = new UserService($userRepository, $deviceRepository);

    $service->sendPasswordResetLink('test@example.com');
});

it('resets password and revokes existing tokens', function (): void {
    $tokenRelation = new class
    {
        public bool $deleted = false;

        public function delete(): void
        {
            $this->deleted = true;
        }
    };

    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('forceFill')
        ->once()
        ->with(Mockery::on(function (array $data): bool {
            return $data['password'] === 'new-password'
                && is_string($data['remember_token'])
                && strlen($data['remember_token']) === 60;
        }))
        ->andReturnSelf();
    $user->shouldReceive('save')->once();
    $user->shouldReceive('tokens')->once()->andReturn($tokenRelation);

    Event::fake([PasswordReset::class]);

    Password::shouldReceive('broker')
        ->once()
        ->with('users')
        ->andReturnSelf();
    Password::shouldReceive('reset')
        ->once()
        ->with(
            [
                'email' => 'test@example.com',
                'token' => 'reset-token',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ],
            Mockery::type(Closure::class),
        )
        ->andReturnUsing(function (array $payload, Closure $callback) use ($user) {
            $callback($user, $payload['password']);

            return Password::PASSWORD_RESET;
        });

    $userRepository = Mockery::mock(IUserRepository::class);
    $deviceRepository = Mockery::mock(IUserDeviceRepository::class);
    $service = new UserService($userRepository, $deviceRepository);

    expect($service->resetPassword('test@example.com', 'reset-token', 'new-password'))->toBeTrue();
    Event::assertDispatched(PasswordReset::class);
    expect($tokenRelation->deleted)->toBeTrue();
});

it('returns false when password reset token is invalid', function (): void {
    Password::shouldReceive('broker')
        ->once()
        ->with('users')
        ->andReturnSelf();
    Password::shouldReceive('reset')
        ->once()
        ->andReturn(Password::INVALID_TOKEN);

    $userRepository = Mockery::mock(IUserRepository::class);
    $deviceRepository = Mockery::mock(IUserDeviceRepository::class);
    $service = new UserService($userRepository, $deviceRepository);

    expect($service->resetPassword('test@example.com', 'invalid-token', 'new-password'))->toBeFalse();
});
