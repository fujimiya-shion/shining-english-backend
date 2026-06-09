<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Transaction;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Api\ApiController;
use App\Integrations\Payments\Factories\PaymentStrategyFactory;
use App\Traits\Jsonable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class PaymentWebhookController extends ApiController
{
    use Jsonable;

    public function payos(Request $request): JsonResponse
    {
        try {
            $order = PaymentStrategyFactory::make(PaymentMethod::Payos)->handleWebhook($request->all());

            return $this->success(
                data: [
                    'processed' => true,
                    'order_id' => $order?->id,
                ],
            );
        } catch (RuntimeException $exception) {
            return $this->error($exception->getMessage(), 422);
        }
    }
}
