<?php

namespace App\Repositories\Cart;

use App\Repositories\IRepository;
use Illuminate\Support\Collection;

interface ICartRepository extends IRepository
{
    public function itemsByUserId(int $userId): Collection;

    /**
     * @return array{items: int, quantity: int}
     */
    public function countByUserId(int $userId): array;

    public function clearByUserId(int $userId): void;
}
