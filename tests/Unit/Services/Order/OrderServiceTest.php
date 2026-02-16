<?php

namespace Tests\Unit\Services\Order;

use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use App\Services\Order\IOrderService;
use App\Services\Order\OrderService;

it('implements shared service contract', function (): void {
    $model = new Order;
    $repository = new OrderRepository($model);
    $service = new OrderService($repository);

    assertServiceContract($service, IOrderService::class, $repository);
});
