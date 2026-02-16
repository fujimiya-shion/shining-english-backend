<?php

use App\Models\OrderItem;
use App\Repositories\OrderItem\IOrderItemRepository;
use App\Repositories\OrderItem\OrderItemRepository;

it('implements shared repository contract', function (): void {
    $model = new OrderItem;
    $repository = new OrderItemRepository($model);

    assertRepositoryContract($repository, IOrderItemRepository::class, $model);
});
