<?php

declare(strict_types=1);

namespace App\Integrations\Payments\Factories;

use App\Enums\PaymentMethod;
use App\Integrations\Payments\Contracts\PaymentStrategy;
use App\Integrations\Payments\Strategies\CodPaymentStrategy;
use App\Integrations\Payments\Strategies\PayosPaymentStrategy;

class PaymentStrategyFactory
{
    public static function make(PaymentMethod $paymentMethod): PaymentStrategy
    {
        return match ($paymentMethod) {
            PaymentMethod::Cod => app(CodPaymentStrategy::class),
            PaymentMethod::Payos => app(PayosPaymentStrategy::class),
        };
    }
}
