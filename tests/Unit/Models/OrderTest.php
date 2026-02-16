<?php

use App\Models\Order;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

it('defines fillable attributes', function (): void {
    $order = new Order;

    expect($order->getFillable())->toEqual([
        'user_id',
        'total_amount',
        'status',
        'placed_at',
    ]);
});

it('casts attributes correctly', function (): void {
    $order = new Order;

    expect($order->getCasts())->toMatchArray([
        'total_amount' => 'integer',
        'placed_at' => 'datetime',
    ]);
});

it('defines user relation', function (): void {
    $order = new Order;

    expect($order->user())->toBeInstanceOf(BelongsTo::class);
});

it('defines items relation', function (): void {
    $order = new Order;

    expect($order->items())->toBeInstanceOf(HasMany::class);
});
