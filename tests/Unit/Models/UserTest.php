<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

it('defines fillable attributes', function (): void {
    $user = new User;

    expect($user->getFillable())->toEqual([
        'name',
        'nickname',
        'email',
        'phone',
        'birthday',
        'avatar',
        'city_id',
        'password',
        'email_verified_at',
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
        'birthday' => 'date',
        'password' => 'hashed',
    ]);
});

it('defines city relation', function (): void {
    $user = new User;

    expect($user->city())->toBeInstanceOf(BelongsTo::class);
});

it('defines quiz attempts relation', function (): void {
    $user = new User;

    expect($user->quizAttempts())->toBeInstanceOf(HasMany::class);
});

it('defines devices relation', function (): void {
    $user = new User;

    expect($user->devices())->toBeInstanceOf(HasMany::class);
});

it('defines enrollments relation', function (): void {
    $user = new User;

    expect($user->enrollments())->toBeInstanceOf(HasMany::class);
});

it('defines blog unlocks relation', function (): void {
    $user = new User;

    expect($user->blogUnlocks())->toBeInstanceOf(HasMany::class);
});

it('hashes password when setting it', function (): void {
    $user = new User;
    $user->password = 'secret';

    expect($user->password)->not->toBe('secret');
    expect(Hash::check('secret', $user->password))->toBeTrue();
});

it('does not rehash an already hashed password', function (): void {
    $user = new User;
    $hashed = Hash::make('secret');

    $user->password = $hashed;

    expect($user->password)->toBe($hashed);
    expect(Hash::check('secret', $user->password))->toBeTrue();
});
