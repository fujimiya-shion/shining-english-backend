<?php

namespace App\Services\Order;

use App\Repositories\Order\IOrderRepository;
use App\Services\Service;

class OrderService extends Service implements IOrderService
{
    public function __construct(IOrderRepository $repository)
    {
        parent::__construct($repository);
    }
}
