<?php

use App\Models\Cart;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

it('defines fillable attributes', function (): void {
    $cart = new Cart;

    expect($cart->getFillable())->toEqual([
        'user_id',
        'course_id',
        'quantity',
    ]);
});

it('casts attributes correctly', function (): void {
    $cart = new Cart;

    expect($cart->getCasts())->toMatchArray([
        'quantity' => 'integer',
    ]);
});

it('defines user relation', function (): void {
    $cart = new Cart;

    expect($cart->user())->toBeInstanceOf(BelongsTo::class);
});

it('defines course relation', function (): void {
    $cart = new Cart;

    expect($cart->course())->toBeInstanceOf(BelongsTo::class);
});
