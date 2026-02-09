<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('defines fillable attributes', function (): void {
    $user = new User;

    expect($user->getFillable())->toEqual([
        'name',
        'email',
        'password',
    ]);
});

it('hides sensitive attributes', function (): void {
    $user = new User;

    expect($user->getHidden())->toEqual([
        'password',
        'remember_token',
    ]);
});

it('casts attributes correctly', function (): void {
    $user = new User;

    expect($user->getCasts())->toMatchArray([
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ]);
});

it('hashes password when setting it', function (): void {
    $user = User::factory()->create([
        'password' => 'secret',
    ]);

    expect($user->password)->not->toBe('secret');
    expect(Hash::check('secret', $user->password))->toBeTrue();
});
