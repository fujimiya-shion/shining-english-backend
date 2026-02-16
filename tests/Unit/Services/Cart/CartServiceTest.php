<?php

namespace Tests\Unit\Services\Cart;

use App\Models\Cart;
use App\Repositories\Cart\CartRepository;
use App\Services\Cart\CartService;
use App\Services\Cart\ICartService;

it('implements shared service contract', function (): void {
    $model = new Cart;
    $repository = new CartRepository($model);
    $service = new CartService($repository);

    assertServiceContract($service, ICartService::class, $repository);
});
