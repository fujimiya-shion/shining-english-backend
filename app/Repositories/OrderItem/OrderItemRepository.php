<?php

namespace App\Repositories\OrderItem;

use App\Models\OrderItem;
use App\Repositories\Repository;

class OrderItemRepository extends Repository implements IOrderItemRepository
{
    public function __construct(OrderItem $model)
    {
        parent::__construct($model);
    }
}
