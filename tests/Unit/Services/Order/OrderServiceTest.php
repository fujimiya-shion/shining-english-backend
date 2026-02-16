<?php

namespace Tests\Unit\Services\Order;

use App\Models\Order;
use App\Repositories\Cart\ICartRepository;
use App\Repositories\Course\ICourseRepository;
use App\Repositories\Order\IOrderRepository;
use App\Repositories\OrderItem\IOrderItemRepository;
use App\Repositories\Order\OrderRepository;
use App\Services\Order\IOrderService;
use App\Services\Order\OrderService;
use Mockery;

it('implements shared service contract', function (): void {
    $model = new Order;
    $repository = new OrderRepository($model);
    $orderItems = Mockery::mock(IOrderItemRepository::class);
    $cart = Mockery::mock(ICartRepository::class);
    $courses = Mockery::mock(ICourseRepository::class);
    $service = new OrderService($repository, $orderItems, $cart, $courses);

    assertServiceContract($service, IOrderService::class, $repository);
});
