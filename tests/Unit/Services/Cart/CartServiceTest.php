<?php

namespace Tests\Unit\Services\Cart;

use App\Models\Cart;
use App\Models\Course;
use App\Models\User;
use App\Repositories\Cart\ICartRepository;
use App\Repositories\Cart\CartRepository;
use App\Services\Cart\CartService;
use App\Services\Cart\ICartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

it('implements shared service contract', function (): void {
    $model = new Cart;
    $repository = new CartRepository($model);
    $service = new CartService($repository);

    assertServiceContract($service, ICartService::class, $repository);
});

it('returns cart items via repository', function (): void {
    $items = new Collection([new Cart]);

    $repository = Mockery::mock(ICartRepository::class);
    $repository->shouldReceive('itemsByUserId')
        ->once()
        ->with(10)
        ->andReturn($items);

    $service = new CartService($repository);

    $result = $service->itemsByUserId(10);

    expect($result)->toBe($items);
});

it('returns cart counts via repository', function (): void {
    $repository = Mockery::mock(ICartRepository::class);
    $repository->shouldReceive('countByUserId')
        ->once()
        ->with(10)
        ->andReturn([
            'items' => 2,
            'quantity' => 3,
        ]);

    $service = new CartService($repository);

    $result = $service->countByUserId(10);

    expect($result)->toEqual([
        'items' => 2,
        'quantity' => 3,
    ]);
});

it('clears cart via repository', function (): void {
    $repository = Mockery::mock(ICartRepository::class);
    $repository->shouldReceive('clearByUserId')
        ->once()
        ->with(10)
        ->andReturnNull();

    $service = new CartService($repository);

    $service->clearByUserId(10);

    expect(true)->toBeTrue();
});
