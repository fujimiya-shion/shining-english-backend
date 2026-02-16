<?php

namespace App\Services\OrderItem;

use App\Repositories\OrderItem\IOrderItemRepository;
use App\Services\Service;

class OrderItemService extends Service implements IOrderItemService
{
    public function __construct(IOrderItemRepository $repository)
    {
        parent::__construct($repository);
    }
}
