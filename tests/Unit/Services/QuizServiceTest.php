<?php

use App\Repositories\Quiz\IQuizRepository;
use App\Services\Quiz\IQuizService;
use App\Services\Quiz\QuizService;
use Tests\TestCase;

uses(TestCase::class);

it('implements quiz service contract', function (): void {
    $repository = \Mockery::mock(IQuizRepository::class);
    $service = new QuizService($repository);

    assertServiceContract($service, IQuizService::class, $repository);
});
