<?php

use App\Models\User;
use App\Repositories\User\IUserRepository;
use App\Repositories\User\UserRepository;

it('implements shared repository contract', function (): void {
    $model = new User;
    $repository = new UserRepository($model);

    assertRepositoryContract($repository, IUserRepository::class, $model);
});
