<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\User\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\User\LoginRequest;
use App\Http\Requests\Api\V1\User\RegisterRequest;
use App\Http\Requests\Api\V1\User\ResetPasswordRequest;
use App\Services\User\IUserService;
use App\Traits\Jsonable;
use App\ValueObjects\DeviceInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AuthController extends ApiController {
    use Jsonable;

    public function __construct(
        private IUserService $service
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $result = $this->service->register(
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['password'],
            );

            return $this->created($result->toArray(), 'Register successfully');
        } catch (Throwable $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $device = DeviceInfo::fromArray([
                'device_identifier' => $data['device_identifier'],
                'device_name' => $data['device_name'] ?? null,
                'platform' => $data['platform'] ?? null,
                'ip_address' => $data['ip_address'] ?? $request->ip(),
                'user_agent' => $data['user_agent'] ?? $request->userAgent(),
            ]);

            $result = $this->service->login($data['email'], $data['password'], $device);

            return $this->success('Login successfully', $result->toArray());
        } catch (Throwable $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(data: $request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->header('User-Authorization');
        $loggedOut = is_string($token)
            ? $this->service->logoutByToken($token)
            : false;

        if (! $loggedOut) {
            return $this->error('Unauthenticated', 401);
        }

        return $this->success('Logged out');
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->service->sendPasswordResetLink($request->validated('email'));

        return $this->success('If your email exists, a password reset link has been sent.');
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();

        $reset = $this->service->resetPassword(
            $data['email'],
            $data['token'],
            $data['password'],
        );

        if (! $reset) {
            return $this->error('Invalid or expired reset token.', 422);
        }

        return $this->success('Password reset successfully.');
    }
}
