<?php

namespace App\Services\Cart;

use App\Services\IService;
use Illuminate\Support\Collection;

interface ICartService extends IService
{
    public function itemsByUserId(int $userId): Collection;

    /**
     * @return array{items: int, quantity: int}
     */
    public function countByUserId(int $userId): array;

    public function clearByUserId(int $userId): void;
}
