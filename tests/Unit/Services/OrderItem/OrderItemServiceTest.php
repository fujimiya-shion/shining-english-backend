<?php

namespace Tests\Unit\Services\OrderItem;

use App\Models\OrderItem;
use App\Repositories\OrderItem\OrderItemRepository;
use App\Services\OrderItem\IOrderItemService;
use App\Services\OrderItem\OrderItemService;

it('implements shared service contract', function (): void {
    $model = new OrderItem;
    $repository = new OrderItemRepository($model);
    $service = new OrderItemService($repository);

    assertServiceContract($service, IOrderItemService::class, $repository);
});
