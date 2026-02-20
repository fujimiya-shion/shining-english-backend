<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Filament\Widgets\RevenueTrendChart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('revenue trend chart sums paid orders', function (): void {
    $user = User::factory()->create();

    Order::query()->create([
        'user_id' => $user->id,
        'total_amount' => 200000,
        'status' => OrderStatus::Paid,
        'payment_method' => PaymentMethod::Cod,
        'placed_at' => now(),
    ]);

    $widget = new RevenueTrendChart;

    $data = invokeProtectedMethod($widget, 'getData');

    expect($data)->toHaveKeys(['datasets', 'labels']);
    expect($data['datasets'][0]['data'])->toHaveCount(14);
    expect(array_sum($data['datasets'][0]['data']))->toBe(200000);
});

test('revenue trend chart pins y axis at zero', function (): void {
    $widget = new RevenueTrendChart;

    $options = invokeProtectedMethod($widget, 'getOptions');

    expect($options['scales']['y']['min'])->toBe(0);
    expect($options['scales']['y']['beginAtZero'])->toBeTrue();
});

test('revenue trend chart is line type', function (): void {
    $widget = new RevenueTrendChart;

    $type = invokeProtectedMethod($widget, 'getType');

    expect($type)->toBe('line');
});
