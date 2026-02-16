<?php

use App\Models\Order;
use App\Repositories\Order\IOrderRepository;
use App\Repositories\Order\OrderRepository;

it('implements shared repository contract', function (): void {
    $model = new Order;
    $repository = new OrderRepository($model);

    assertRepositoryContract($repository, IOrderRepository::class, $model);
});
