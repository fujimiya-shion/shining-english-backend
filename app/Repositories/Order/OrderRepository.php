<?php

namespace App\Repositories\Order;

use App\Models\Order;
use App\Repositories\Repository;

class OrderRepository extends Repository implements IOrderRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }
}
