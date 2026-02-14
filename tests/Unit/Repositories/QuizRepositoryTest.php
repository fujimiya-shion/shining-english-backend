<?php

use App\Models\Quiz;
use App\Repositories\Quiz\IQuizRepository;
use App\Repositories\Quiz\QuizRepository;
use Tests\TestCase;

uses(TestCase::class);

it('implements quiz repository contract', function (): void {
    $model = new Quiz;
    $repository = new QuizRepository($model);

    assertRepositoryContract($repository, IQuizRepository::class, $model);
});
