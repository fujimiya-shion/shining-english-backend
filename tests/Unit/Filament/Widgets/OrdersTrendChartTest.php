<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Filament\Widgets\OrdersTrendChart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('orders trend chart builds 14 day series', function (): void {
    $user = User::factory()->create();

    Order::query()->create([
        'user_id' => $user->id,
        'total_amount' => 120000,
        'status' => OrderStatus::Pending,
        'payment_method' => PaymentMethod::Cod,
        'placed_at' => now(),
    ]);

    $widget = new OrdersTrendChart;

    $data = invokeProtectedMethod($widget, 'getData');

    expect($data)->toHaveKeys(['datasets', 'labels']);
    expect($data['datasets'][0]['data'])->toHaveCount(14);
    expect($data['labels'])->toHaveCount(14);
    expect(array_sum($data['datasets'][0]['data']))->toBe(1);
});

test('orders trend chart pins y axis at zero', function (): void {
    $widget = new OrdersTrendChart;

    $options = invokeProtectedMethod($widget, 'getOptions');

    expect($options['scales']['y']['min'])->toBe(0);
    expect($options['scales']['y']['beginAtZero'])->toBeTrue();
});

test('orders trend chart is line type', function (): void {
    $widget = new OrdersTrendChart;

    $type = invokeProtectedMethod($widget, 'getType');

    expect($type)->toBe('line');
});
