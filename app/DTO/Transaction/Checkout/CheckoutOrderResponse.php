<?php

declare(strict_types=1);

namespace App\DTO\Transaction\Checkout;

use App\Models\Order;

class CheckoutOrderResponse
{
    public function __construct(
        public Order $order,
        public ?CheckoutPaymentActionResponse $paymentAction = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'order' => $this->order->toArray(),
            'payment_action' => $this->paymentAction?->toArray(),
        ], static fn (mixed $value): bool => $value !== null);
    }
}
