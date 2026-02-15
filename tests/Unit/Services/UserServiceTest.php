<?php

use App\DTO\User\Auth\LoginResponse;
use App\DTO\User\Auth\RegisterResponse;
use App\Models\User;
use App\Repositories\User\IUserDeviceRepository;
use App\Repositories\User\IUserRepository;
use App\Services\User\UserService;
use App\ValueObjects\DeviceInfo;
use Illuminate\Database\Eloquent\Collection;
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

    expect($response)->toBeInstanceOf(LoginResponse::class);
    expect($response->token)->toBe('test-token');
    expect($response->user)->toBe($user);
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

    $service = \Mockery::mock(UserService::class, [$repository, $deviceRepository])
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();
    $service->shouldReceive('createUserDevice')
        ->once()
        ->with($user, \Mockery::type(DeviceInfo::class), 10)
        ->andReturnNull();

    $response = $service->login(
        'test@example.com',
        'secret',
        DeviceInfo::fromArray([
            'device_identifier' => 'device-1',
            'device_name' => 'iPhone',
        ])
    );

    expect($response)->toBeInstanceOf(RegisterResponse::class);
    expect($response->token)->toBe('login-token');
    expect($response->user)->toBe($user);
});
