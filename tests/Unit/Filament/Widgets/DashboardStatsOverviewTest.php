<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Filament\Widgets\DashboardStatsOverview;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

test('dashboard stats overview defines four stats', function (): void {
    $widget = new DashboardStatsOverview;

    $stats = invokeProtectedMethod($widget, 'getStats');

    expect($stats)->toHaveCount(4);
});

test('dashboard stats overview formats trend labels', function (): void {
    $widget = new DashboardStatsOverview;

    $up = invokeProtectedMethod($widget, 'trendLabel', [12, 10]);
    $down = invokeProtectedMethod($widget, 'trendLabel', [8, 10]);
    $flat = invokeProtectedMethod($widget, 'trendLabel', [5, 5]);
    $withSuffix = invokeProtectedMethod($widget, 'trendLabel', [20, 10, 'VND']);

    expect($up[0])->toContain('up');
    expect($up[2])->toBe('success');
    expect($down[0])->toContain('down');
    expect($down[2])->toBe('danger');
    expect($flat[0])->toBe('No change vs last 30 days');
    expect($flat[2])->toBe('gray');
    expect($withSuffix[0])->toContain('VND');
});

test('dashboard stats overview pluralizes labels', function (): void {
    $widget = new DashboardStatsOverview;

    $single = invokeProtectedMethod($widget, 'pluralize', [1, 'user']);
    $many = invokeProtectedMethod($widget, 'pluralize', [2, 'user']);

    expect($single)->toBe('1 user');
    expect($many)->toBe('2 users');
});

test('dashboard stats overview builds sparklines', function (): void {
    $widget = new DashboardStatsOverview;

    $countSeries = invokeProtectedMethod($widget, 'countSparkline', [\App\Models\User::query(), 'created_at']);
    $sumSeries = invokeProtectedMethod($widget, 'sumSparkline', [\App\Models\Order::query(), 'placed_at', 'total_amount']);

    expect($countSeries)->toHaveCount(7);
    expect($sumSeries)->toHaveCount(7);
});

test('dashboard stats overview maps sparkline rows into series', function (): void {
    $now = Carbon::parse('2026-02-20 10:00:00');
    Carbon::setTestNow($now);

    $user = User::factory()->create([
        'created_at' => $now->copy()->subDay(),
    ]);

    Order::query()->create([
        'user_id' => $user->id,
        'total_amount' => 50000,
        'status' => OrderStatus::Paid,
        'payment_method' => PaymentMethod::Cod,
        'placed_at' => $now->copy()->subDay(),
    ]);

    $widget = new DashboardStatsOverview;

    $countSeries = invokeProtectedMethod($widget, 'countSparkline', [User::query(), 'created_at']);
    $sumSeries = invokeProtectedMethod($widget, 'sumSparkline', [Order::query(), 'placed_at', 'total_amount']);

    expect(array_sum($countSeries))->toBeGreaterThan(0);
    expect(array_sum($sumSeries))->toBeGreaterThan(0);

    Carbon::setTestNow();
});
