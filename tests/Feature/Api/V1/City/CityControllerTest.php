<?php

use App\Http\Controllers\Api\V1\City\CityController;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists cities in configured order', function (): void {
    City::query()->create(['name' => 'Beta', 'sort_order' => 2]);
    City::query()->create(['name' => 'Alpha', 'sort_order' => 1]);

    $response = (new CityController)->index();

    assertJsonResponsePayload($response, 200, [
        'status' => true,
        'status_code' => 200,
    ]);
    expect($response->getData(true)['data'][0]['name'])->toBe('Alpha');
});
