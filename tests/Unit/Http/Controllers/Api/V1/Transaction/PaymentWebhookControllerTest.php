<?php

use App\Http\Controllers\Api\V1\Transaction\PaymentWebhookController;
use App\Integrations\Payments\Contracts\PaymentStrategy;
use App\Integrations\Payments\Strategies\PayosPaymentStrategy;
use App\Models\Order;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class);

it('handles payos webhook success and failure responses', function (): void {
    $order = new Order;
    $order->id = 900;

    $strategy = Mockery::mock(PaymentStrategy::class);
    $strategy->shouldReceive('handleWebhook')->once()->andReturn($order);
    app()->instance(PayosPaymentStrategy::class, $strategy);

    $success = (new PaymentWebhookController)->payos(Request::create('/webhook', 'POST', ['ok' => true]));
    assertJsonResponsePayload($success, 200, [
        'status' => true,
        'status_code' => 200,
        'data' => ['processed' => true, 'order_id' => 900],
    ]);

    $failingStrategy = Mockery::mock(PaymentStrategy::class);
    $failingStrategy->shouldReceive('handleWebhook')->once()->andThrow(new RuntimeException('bad webhook'));
    app()->instance(PayosPaymentStrategy::class, $failingStrategy);

    $failure = (new PaymentWebhookController)->payos(Request::create('/webhook', 'POST', ['bad' => true]));
    assertJsonResponsePayload($failure, 422, [
        'status' => false,
        'status_code' => 422,
        'message' => 'bad webhook',
    ]);
});
