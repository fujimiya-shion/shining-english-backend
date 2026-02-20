<?php

use App\DTO\User\Auth\LoginResponse;
use App\Models\User;

it('converts login response to array', function (): void {
    $user = new User;
    $response = new LoginResponse('token-1', $user);

    expect($response->toArray())->toEqual([
        'token' => 'token-1',
        'user' => $user,
    ]);
});
