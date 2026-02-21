<?php

namespace Tests\Unit\Services\Order;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Repositories\Cart\ICartRepository;
use App\Repositories\Course\ICourseRepository;
use App\Repositories\Order\IOrderRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\OrderItem\IOrderItemRepository;
use App\Services\Enrollment\IEnrollmentService;
use App\Services\Order\IOrderService;
use App\Services\Order\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Mockery;
use RuntimeException;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

it('implements shared service contract', function (): void {
    $model = new Order;
    $repository = new OrderRepository($model);
    $orderItems = Mockery::mock(IOrderItemRepository::class);
    $cart = Mockery::mock(ICartRepository::class);
    $courses = Mockery::mock(ICourseRepository::class);
    $enrollments = Mockery::mock(IEnrollmentService::class);
    $service = new OrderService($repository, $orderItems, $cart, $courses, $enrollments);

    assertServiceContract($service, IOrderService::class, $repository);
});

it('throws when creating order from empty cart', function (): void {
    $orders = Mockery::mock(IOrderRepository::class);
    $orderItems = Mockery::mock(IOrderItemRepository::class);
    $cart = Mockery::mock(ICartRepository::class);
    $courses = Mockery::mock(ICourseRepository::class);
    $enrollments = Mockery::mock(IEnrollmentService::class);

    $cart->shouldReceive('itemsByUserId')
        ->once()
        ->with(10)
        ->andReturn(new Collection);

    $service = new OrderService($orders, $orderItems, $cart, $courses, $enrollments);

    expect(fn () => $service->createFromCart(10, PaymentMethod::Cod))
        ->toThrow(RuntimeException::class, 'Cart is empty');
});

it('throws when buying now with missing course', function (): void {
    $orders = Mockery::mock(IOrderRepository::class);
    $orderItems = Mockery::mock(IOrderItemRepository::class);
    $cart = Mockery::mock(ICartRepository::class);
    $courses = Mockery::mock(ICourseRepository::class);
    $enrollments = Mockery::mock(IEnrollmentService::class);

    $courses->shouldReceive('getById')
        ->once()
        ->with(99)
        ->andReturnNull();

    $service = new OrderService($orders, $orderItems, $cart, $courses, $enrollments);

    expect(fn () => $service->createBuyNow(10, 99, 1, PaymentMethod::Cod))
        ->toThrow(RuntimeException::class, 'Course not found');
});

it('returns false when cancelling missing order', function (): void {
    $orders = Mockery::mock(IOrderRepository::class);
    $orderItems = Mockery::mock(IOrderItemRepository::class);
    $cart = Mockery::mock(ICartRepository::class);
    $courses = Mockery::mock(ICourseRepository::class);
    $enrollments = Mockery::mock(IEnrollmentService::class);

    $orders->shouldReceive('findByUserId')
        ->once()
        ->with(10, 999)
        ->andReturnNull();

    $service = new OrderService($orders, $orderItems, $cart, $courses, $enrollments);

    expect($service->cancelByUserId(10, 999))->toBeFalse();
});

it('enrolls user after creating buy now order', function (): void {
    $orders = Mockery::mock(IOrderRepository::class);
    $orderItems = Mockery::mock(IOrderItemRepository::class);
    $cart = Mockery::mock(ICartRepository::class);
    $courses = Mockery::mock(ICourseRepository::class);
    $enrollments = Mockery::mock(IEnrollmentService::class);

    $course = new \App\Models\Course;
    $course->id = 55;
    $course->price = 120000;

    $courses->shouldReceive('getById')
        ->once()
        ->with(55)
        ->andReturn($course);

    $order = new Order([
        'user_id' => 10,
        'total_amount' => 120000,
        'status' => OrderStatus::Pending,
        'payment_method' => PaymentMethod::Cod,
        'placed_at' => now(),
    ]);
    $order->id = 99;

    $orders->shouldReceive('create')
        ->once()
        ->andReturn($order);

    $orderItems->shouldReceive('create')
        ->once()
        ->with([
            'order_id' => 99,
            'course_id' => 55,
            'quantity' => 1,
            'price' => 120000,
        ])
        ->andReturn(new \App\Models\OrderItem);

    $enrollments->shouldReceive('enroll')
        ->once()
        ->with(10, 55, 99)
        ->andReturn(Mockery::mock(\App\Models\Enrollment::class));

    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(function (callable $callback) {
            return $callback();
        });

    DB::shouldReceive('afterCommit')
        ->once()
        ->andReturnUsing(function (callable $callback) {
            $callback();
        });

    $service = new OrderService($orders, $orderItems, $cart, $courses, $enrollments);

    $result = $service->createBuyNow(10, 55, 1, PaymentMethod::Cod);

    expect($result)->toBe($order);
});
