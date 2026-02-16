<?php

namespace App\Services\Cart;

use App\Repositories\Cart\ICartRepository;
use App\Services\Service;

class CartService extends Service implements ICartService
{
    public function __construct(ICartRepository $repository)
    {
        parent::__construct($repository);
    }
}
