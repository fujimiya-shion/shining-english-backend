<?php

use App\DTO\User\Auth\LoginResponse;
use App\DTO\User\Auth\RegisterResponse;
use App\Models\User;
use App\Repositories\User\IUserDeviceRepository;
use App\Repositories\User\IUserRepository;
use App\Services\User\UserService;
use App\ValueObjects\DeviceInfo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Tests\TestCase;

uses(TestCase::class);

it('registers a user and returns a token response', function (): void {
    $user = new class extends User {
        public function createToken(string $name, array $abilities = ['*'], ?\DateTimeInterface $expiresAt = null): object
        {
            return new class {
                public string $plainTextToken = 'test-token';
            };
        }
    };
    $user->id = 1;

    $repository = \Mockery::mock(IUserRepository::class);
    $repository->shouldReceive('create')
        ->once()
        ->with([
            'email' => 'test@example.com',
            'password' => 'secret',
        ])
        ->andReturn($user);

    $deviceRepository = \Mockery::mock(IUserDeviceRepository::class);

    $service = new UserService($repository, $deviceRepository);

    $response = $service->register('test@example.com', 'secret');

    expect($response)->toBeInstanceOf(RegisterResponse::class);
    expect($response->token)->toBe('test-token');
    expect($response->user)->toBe($user);
});

it('throws when register result is not a user instance', function (): void {
    $repository = \Mockery::mock(IUserRepository::class);
    $repository->shouldReceive('create')
        ->once()
        ->with([
            'email' => 'test@example.com',
            'password' => 'secret',
        ])
        ->andReturn(new \App\Models\UserDevice);

    $deviceRepository = \Mockery::mock(IUserDeviceRepository::class);
    $service = new UserService($repository, $deviceRepository);

    expect(fn () => $service->register('test@example.com', 'secret'))
        ->toThrow(Exception::class, 'return model is not instance of user');
});

it('logs in a user and returns a token response', function (): void {
    $user = new class extends User {
        public function createToken(string $name, array $abilities = ['*'], ?\DateTimeInterface $expiresAt = null): object
        {
            return new class {
                public string $plainTextToken = 'login-token';
                public object $accessToken;

                public function __construct()
                {
                    $this->accessToken = (object) ['id' => 10];
                }
            };
        }
    };

    $user->password = \Illuminate\Support\Facades\Hash::make('secret');

    $repository = \Mockery::mock(IUserRepository::class);
    $repository->shouldReceive('getBy')
        ->once()
        ->with(['email' => 'test@example.com'])
        ->andReturn(new Collection([$user]));

    $deviceRepository = \Mockery::mock(IUserDeviceRepository::class);
    $now = Carbon::parse('2025-01-01 10:00:00');
    Carbon::setTestNow($now);
    $deviceRepository->shouldReceive('create')
        ->once()
        ->with(\Mockery::on(function (array $data) use ($user, $now): bool {
            return $data['user_id'] === $user->id
                && $data['personal_access_token_id'] === 10
                && $data['device_identifier'] === 'device-1'
                && $data['device_name'] === 'iPhone'
                && $data['platform'] === null
                && $data['ip_address'] === null
                && $data['user_agent'] === null
                && $data['logged_in_at']->eq($now)
                && $data['last_seen_at']->eq($now);
        }))
        ->andReturn(new \App\Models\UserDevice);

    $service = new UserService($repository, $deviceRepository);

    $response = $service->login(
        'test@example.com',
        'secret',
        DeviceInfo::fromArray([
            'device_identifier' => 'device-1',
            'device_name' => 'iPhone',
        ])
    );
    Carbon::setTestNow();

    expect($response)->toBeInstanceOf(LoginResponse::class);
    expect($response->token)->toBe('login-token');
    expect($response->user)->toBe($user);
});

it('throws when credentials are invalid', function (): void {
    $repository = \Mockery::mock(IUserRepository::class);
    $repository->shouldReceive('getBy')
        ->once()
        ->with(['email' => 'missing@example.com'])
        ->andReturn(new Collection([]));

    $deviceRepository = \Mockery::mock(IUserDeviceRepository::class);
    $service = new UserService($repository, $deviceRepository);

    expect(fn () => $service->login(
        'missing@example.com',
        'secret',
        DeviceInfo::fromArray(['device_identifier' => 'device-1'])
    ))->toThrow(Exception::class, 'Invalid credentials');
});
