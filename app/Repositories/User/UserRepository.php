<?php
namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\Repository;
class UserRepository extends Repository implements IUserRepository {
    public function __construct(User $model) {
        parent::__construct($model);
    }
}