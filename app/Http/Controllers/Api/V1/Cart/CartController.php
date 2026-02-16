<?php

namespace App\Http\Controllers\Api\V1\Cart;

use App\Http\Controllers\Api\ApiController;
use App\Services\Cart\ICartService;
use App\Traits\Jsonable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends ApiController
{
    use Jsonable;

    public function __construct(
        protected ICartService $service
    ) {}

    public function items(Request $request): JsonResponse
    {
        $user = $request->user();
        $items = $this->service->itemsByUserId($user->id);

        return $this->success(data: $items);
    }

    public function count(Request $request): JsonResponse
    {
        $user = $request->user();
        $counts = $this->service->countByUserId($user->id);

        return $this->success(data: $counts);
    }

    public function clear(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->service->clearByUserId($user->id);

        return $this->deleted('Cart cleared');
    }
}
