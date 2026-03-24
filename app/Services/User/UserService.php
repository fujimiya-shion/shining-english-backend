<?php
namespace App\Services\User;

use App\DTO\User\Auth\LoginResponse;
use App\DTO\User\Auth\RegisterResponse;
use App\Jobs\InitUserStarJob;
use App\Jobs\SendEmailVerificationJob;
use App\Models\User;
use App\Repositories\User\IUserDeviceRepository;
use App\Repositories\User\IUserRepository;
use App\Services\Service;
use App\ValueObjects\DeviceInfo;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
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
                if ($created->id !== null) {
                    dispatch(new InitUserStarJob($created->id));
                    dispatch(new SendEmailVerificationJob($created->id));
                }
                return new RegisterResponse($created);
            }
            throw new Exception("return model is not instance of user");

        } catch(Throwable $e) {
            throw $e;
        }
    }

    public function login(string $email, string $password, DeviceInfo $device): LoginResponse {
        try {
            $user = $this->userRepository->findByEmail($email);

            if (!$user instanceof User || !Hash::check($password, $user->password)) {
                throw new Exception('Invalid credentials');
            }

            if (! $user->hasVerifiedEmail()) {
                throw new Exception('Email is not verified.');
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

    public function sendPasswordResetLink(string $email): void
    {
        Password::broker('users')->sendResetLink([
            'email' => $email,
        ]);
    }

    public function resetPassword(string $email, string $token, string $password): bool
    {
        $status = Password::broker('users')->reset(
            [
                'email' => $email,
                'token' => $token,
                'password' => $password,
                'password_confirmation' => $password,
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET;
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
