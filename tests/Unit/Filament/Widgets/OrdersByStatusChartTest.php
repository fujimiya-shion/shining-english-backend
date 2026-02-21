<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Filament\Widgets\OrdersByStatusChart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('orders by status chart covers all statuses', function (): void {
    $user = User::factory()->create();

    Order::query()->create([
        'user_id' => $user->id,
        'total_amount' => 100000,
        'status' => OrderStatus::Pending,
        'payment_method' => PaymentMethod::Cod,
        'placed_at' => now(),
    ]);

    Order::query()->create([
        'user_id' => $user->id,
        'total_amount' => 150000,
        'status' => OrderStatus::Paid,
        'payment_method' => PaymentMethod::Cod,
        'placed_at' => now(),
    ]);

    $widget = new OrdersByStatusChart;

    $data = invokeProtectedMethod($widget, 'getData');

    expect($data)->toHaveKeys(['datasets', 'labels']);
    expect($data['labels'])->toHaveCount(count(OrderStatus::cases()));
    expect(array_sum($data['datasets'][0]['data']))->toBe(2);
});

test('orders by status chart is doughnut type', function (): void {
    $widget = new OrdersByStatusChart;

    $type = invokeProtectedMethod($widget, 'getType');

    expect($type)->toBe('doughnut');
});
