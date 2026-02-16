<?php

namespace App\Services\Cart;

use App\Repositories\Cart\ICartRepository;
use App\Services\Service;
use Illuminate\Support\Collection;

class CartService extends Service implements ICartService
{
    protected ICartRepository $cartRepository;
    public function __construct(ICartRepository $repository)
    {
        parent::__construct($repository);
        $this->cartRepository = $repository;
    }

    public function itemsByUserId(int $userId): Collection
    {
        return $this->cartRepository->itemsByUserId($userId);
    }

    public function countByUserId(int $userId): array
    {
        return $this->cartRepository->countByUserId($userId);
    }

    public function clearByUserId(int $userId): void
    {
        $this->cartRepository->clearByUserId($userId);
    }
}
