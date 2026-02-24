<?php
namespace App\Services\User;

use App\DTO\User\Auth\LoginResponse;
use App\DTO\User\Auth\RegisterResponse;
use App\Jobs\InitUserStarJob;
use App\Models\User;
use App\Repositories\User\IUserDeviceRepository;
use App\Repositories\User\IUserRepository;
use App\Services\Service;
use App\Services\Star\IStarService;
use App\ValueObjects\DeviceInfo;
use Exception;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Throwable;
class UserService extends Service implements IUserService {
    protected IUserRepository $userRepository;
    protected IUserDeviceRepository $userDeviceRepository;
    public function __construct(
        IUserRepository $repository,
        IUserDeviceRepository $userDeviceRepository,
    ) {
        parent::__construct($repository);
        $this->userRepository = $repository;
        $this->userDeviceRepository = $userDeviceRepository;
    }

    public function register(string $name, string $email, string $phone, string $password): RegisterResponse {
        try {
            $created = $this->userRepository->create([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password' => $password,
            ]);

            if($created instanceof User) {
                $token = $created->createToken('user_auth_token')->plainTextToken;
                dispatch(new InitUserStarJob(
                    $created, 
                    app(IStarService::class)
                ));
                return new RegisterResponse($token, $created);
            }
            throw new Exception("return model is not instance of user");

        } catch(Throwable $e) {
            throw $e;
        }
    }

    public function login(string $email, string $password, DeviceInfo $device): LoginResponse {
        try {
            $user = $this->userRepository->getBy(['email' => $email])->first();

            if (!$user instanceof User || !Hash::check($password, $user->password)) {
                throw new Exception('Invalid credentials');
            }

            $tokenResult = $user->createToken('user_auth_token');
            $this->createUserDevice($user, $device, $tokenResult->accessToken->id ?? null);

            return new LoginResponse($tokenResult->plainTextToken, $user);
        } catch(Throwable $e) {
            throw $e;
        }
    }

    public function logoutByToken(string $token): bool
    {
        $accessToken = PersonalAccessToken::findToken($token);

        if (! $accessToken) {
            return false;
        }

        $this->userDeviceRepository->markLoggedOutByTokenId($accessToken->id);

        $accessToken->delete();

        return true;
    }

    protected function createUserDevice(User $user, DeviceInfo $device, ?int $tokenId): void {
        $this->userDeviceRepository->create([
            'user_id' => $user->id,
            'personal_access_token_id' => $tokenId,
            'device_identifier' => $device->identifier,
            'device_name' => $device->name,
            'platform' => $device->platform,
            'ip_address' => $device->ipAddress,
            'user_agent' => $device->userAgent,
            'logged_in_at' => now(),
            'last_seen_at' => now(),
        ]);
    }
}
