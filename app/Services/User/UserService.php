<?php
namespace App\Services\User;

use App\Repositories\User\IUserRepository;
use App\Services\Service;
class UserService extends Service implements IUserService {
    public function __construct(IUserRepository $repository) {
        parent::__construct($repository);
    }
}