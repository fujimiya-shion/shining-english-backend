<?php

use App\Models\UserDevice;
use App\Repositories\User\IUserDeviceRepository;
use App\Repositories\User\UserDeviceRepository;

it('implements shared repository contract', function (): void {
    $model = new UserDevice;
    $repository = new UserDeviceRepository($model);

    assertRepositoryContract($repository, IUserDeviceRepository::class, $model);
});
