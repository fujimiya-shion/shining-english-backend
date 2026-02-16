<?php

use App\Models\Cart;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Cart\ICartRepository;

it('implements shared repository contract', function (): void {
    $model = new Cart;
    $repository = new CartRepository($model);

    assertRepositoryContract($repository, ICartRepository::class, $model);
});
