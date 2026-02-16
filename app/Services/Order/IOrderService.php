<?php

namespace App\Services\Order;

use App\Enums\PaymentMethod;
use App\Services\IService;
use App\ValueObjects\QueryOption;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Order;

interface IOrderService extends IService
{
    public function listByUserId(int $userId, QueryOption $options): LengthAwarePaginator;

    public function detailByUserId(int $userId, int $orderId): ?Order;

    public function createFromCart(int $userId, PaymentMethod $paymentMethod): Order;

    public function createBuyNow(int $userId, int $courseId, int $quantity, PaymentMethod $paymentMethod): Order;

    public function cancelByUserId(int $userId, int $orderId): bool;
}
