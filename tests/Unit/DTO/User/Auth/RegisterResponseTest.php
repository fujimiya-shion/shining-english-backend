<?php

use App\DTO\User\Auth\RegisterResponse;
use App\Models\User;

it('converts register response to array', function (): void {
    $user = new User;
    $response = new RegisterResponse('token-1', $user);

    expect($response->toArray())->toEqual([
        'token' => 'token-1',
        'user' => $user,
    ]);
});
