<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Repositories\Repository;

class CartRepository extends Repository implements ICartRepository
{
    public function __construct(Cart $model)
    {
        parent::__construct($model);
    }
}
