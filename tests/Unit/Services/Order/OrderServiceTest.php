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
use App\Enums\PaymentMethod;
use Illuminate\Support\Collection;
use Mockery;
use RuntimeException;

it('implements shared service contract', function (): void {
    $model = new Order;
    $repository = new OrderRepository($model);
    $orderItems = Mockery::mock(IOrderItemRepository::class);
    $cart = Mockery::mock(ICartRepository::class);
    $courses = Mockery::mock(ICourseRepository::class);
    $service = new OrderService($repository, $orderItems, $cart, $courses);

    assertServiceContract($service, IOrderService::class, $repository);
});

it('throws when creating order from empty cart', function (): void {
    $orders = Mockery::mock(IOrderRepository::class);
    $orderItems = Mockery::mock(IOrderItemRepository::class);
    $cart = Mockery::mock(ICartRepository::class);
    $courses = Mockery::mock(ICourseRepository::class);

    $cart->shouldReceive('itemsByUserId')
        ->once()
        ->with(10)
        ->andReturn(new Collection);

    $service = new OrderService($orders, $orderItems, $cart, $courses);

    expect(fn () => $service->createFromCart(10, PaymentMethod::Cod))
        ->toThrow(RuntimeException::class, 'Cart is empty');
});

it('throws when buying now with missing course', function (): void {
    $orders = Mockery::mock(IOrderRepository::class);
    $orderItems = Mockery::mock(IOrderItemRepository::class);
    $cart = Mockery::mock(ICartRepository::class);
    $courses = Mockery::mock(ICourseRepository::class);

    $courses->shouldReceive('getById')
        ->once()
        ->with(99)
        ->andReturnNull();

    $service = new OrderService($orders, $orderItems, $cart, $courses);

    expect(fn () => $service->createBuyNow(10, 99, 1, PaymentMethod::Cod))
        ->toThrow(RuntimeException::class, 'Course not found');
});

it('returns false when cancelling missing order', function (): void {
    $orders = Mockery::mock(IOrderRepository::class);
    $orderItems = Mockery::mock(IOrderItemRepository::class);
    $cart = Mockery::mock(ICartRepository::class);
    $courses = Mockery::mock(ICourseRepository::class);

    $orders->shouldReceive('findByUserId')
        ->once()
        ->with(10, 999)
        ->andReturnNull();

    $service = new OrderService($orders, $orderItems, $cart, $courses);

    expect($service->cancelByUserId(10, 999))->toBeFalse();
});
