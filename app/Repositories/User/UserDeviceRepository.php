<?php
namespace App\Repositories\User;

use App\Models\UserDevice;
use App\Repositories\Repository;
class UserDeviceRepository extends Repository implements IUserDeviceRepository {
    public function __construct(UserDevice $model) {
        parent::__construct($model);
    }
}