<?php

use App\Enums\StarTransactionType;
use Tests\TestCase;

uses(TestCase::class);

it('exposes star transaction type values', function (): void {
    $values = StarTransactionType::values();

    expect($values)->toBe([
        StarTransactionType::Increase->value,
        StarTransactionType::Decrease->value,
    ]);
});
