<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

test('stars and star transactions tables have expected columns', function () {
    expect(Schema::hasColumns('stars', [
        'id',
        'user_id',
        'amount',
        'created_at',
        'updated_at',
    ]))->toBeTrue();

    expect(Schema::hasColumns('star_transactions', [
        'id',
        'user_id',
        'amount',
        'type',
        'description',
        'created_at',
        'updated_at',
    ]))->toBeTrue();
});
