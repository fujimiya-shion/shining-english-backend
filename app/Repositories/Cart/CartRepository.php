<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Repositories\Repository;
use Illuminate\Support\Collection;

class CartRepository extends Repository implements ICartRepository
{
    public function __construct(Cart $model)
    {
        parent::__construct($model);
    }

    public function itemsByUserId(int $userId): Collection
    {
        return $this->model
            ->newQuery()
            ->with('course')
            ->where('user_id', $userId)
            ->get();
    }

    public function countByUserId(int $userId): array
    {
        $query = $this->model->newQuery()->where('user_id', $userId);

        return [
            'items' => $query->count(),
            'quantity' => (int) $query->sum('quantity'),
        ];
    }

    public function clearByUserId(int $userId): void
    {
        $this->model->newQuery()->where('user_id', $userId)->delete();
    }
}
