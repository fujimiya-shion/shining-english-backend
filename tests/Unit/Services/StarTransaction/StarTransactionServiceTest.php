<?php

use App\Repositories\StarTransaction\IStarTransactionRepository;
use App\Services\StarTransaction\IStarTransactionService;
use App\Services\StarTransaction\StarTransactionService;
use Tests\TestCase;

uses(TestCase::class);

it('implements star transaction service contract', function (): void {
    $repository = Mockery::mock(IStarTransactionRepository::class);
    $service = new StarTransactionService($repository);

    assertServiceContract($service, IStarTransactionService::class, $repository);
});
