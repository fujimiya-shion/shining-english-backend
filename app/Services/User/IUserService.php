<?php
namespace App\Services\User;

use App\DTO\User\Auth\LoginResponse;
use App\DTO\User\Auth\RegisterResponse;
use App\Services\IService;
use App\ValueObjects\DeviceInfo;
interface IUserService extends IService {
    public function register(string $email, string $password): RegisterResponse;

    public function login(string $email, string $password, DeviceInfo $device): LoginResponse;
}
