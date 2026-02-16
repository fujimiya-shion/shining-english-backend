<?php

use App\Models\Cart;
use App\Models\Course;
use App\Models\User;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Cart\ICartRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

it('implements shared repository contract', function (): void {
    $model = new Cart;
    $repository = new CartRepository($model);

    assertRepositoryContract($repository, ICartRepository::class, $model);
});

it('returns cart items by user id', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create();

    Cart::query()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'quantity' => 1,
    ]);

    $repository = new CartRepository(new Cart);

    $items = $repository->itemsByUserId($user->id);

    expect($items)->toHaveCount(1);
    expect($items->first()?->course_id)->toBe($course->id);
    expect($items->first()?->relationLoaded('course'))->toBeTrue();
});

it('counts cart items and quantity by user id', function (): void {
    $user = User::factory()->create();
    $courseA = Course::factory()->create();
    $courseB = Course::factory()->create();

    Cart::query()->create([
        'user_id' => $user->id,
        'course_id' => $courseA->id,
        'quantity' => 2,
    ]);
    Cart::query()->create([
        'user_id' => $user->id,
        'course_id' => $courseB->id,
        'quantity' => 1,
    ]);

    $repository = new CartRepository(new Cart);

    $counts = $repository->countByUserId($user->id);

    expect($counts['items'])->toBe(2);
    expect($counts['quantity'])->toBe(3);
});

it('clears cart items by user id', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create();

    Cart::query()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'quantity' => 1,
    ]);

    $repository = new CartRepository(new Cart);

    $repository->clearByUserId($user->id);

    expect(Cart::query()->where('user_id', $user->id)->count())->toBe(0);
});
