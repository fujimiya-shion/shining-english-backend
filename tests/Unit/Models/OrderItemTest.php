<?php

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

it('defines fillable attributes', function (): void {
    $item = new OrderItem;

    expect($item->getFillable())->toEqual([
        'order_id',
        'course_id',
        'quantity',
        'price',
    ]);
});

it('casts attributes correctly', function (): void {
    $item = new OrderItem;

    expect($item->getCasts())->toMatchArray([
        'quantity' => 'integer',
        'price' => 'integer',
    ]);
});

it('defines order relation', function (): void {
    $item = new OrderItem;

    expect($item->order())->toBeInstanceOf(BelongsTo::class);
});

it('defines course relation', function (): void {
    $item = new OrderItem;

    expect($item->course())->toBeInstanceOf(BelongsTo::class);
});
