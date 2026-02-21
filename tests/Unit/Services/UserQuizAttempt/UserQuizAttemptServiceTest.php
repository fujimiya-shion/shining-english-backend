<?php

namespace Tests\Unit\Services\UserQuizAttempt;

use App\Models\UserQuizAttempt;
use App\Repositories\UserQuizAttempt\IUserQuizAttemptRepository;
use App\Repositories\UserQuizAttempt\UserQuizAttemptRepository;
use App\Services\UserQuizAttempt\IUserQuizAttemptService;
use App\Services\UserQuizAttempt\UserQuizAttemptService;
use App\ValueObjects\QueryOption;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

uses(TestCase::class);

it('implements shared service contract', function (): void {
    $model = new UserQuizAttempt;
    $repository = new UserQuizAttemptRepository($model);
    $service = new UserQuizAttemptService($repository);

    assertServiceContract($service, IUserQuizAttemptService::class, $repository);
});

it('records quiz attempt via repository', function (): void {
    $attempt = new UserQuizAttempt;

    $repository = Mockery::mock(IUserQuizAttemptRepository::class);
    $repository->shouldReceive('create')
        ->once()
        ->with(Mockery::on(function (array $data): bool {
            return $data['user_id'] === 10
                && $data['quiz_id'] === 99
                && $data['score_percent'] === 75.5
                && $data['passed'] === true
                && isset($data['submitted_at']);
        }))
        ->andReturn($attempt);

    $service = new UserQuizAttemptService($repository);

    $result = $service->recordAttempt(10, 99, 75.5, true);

    expect($result)->toBe($attempt);
});

it('returns attempt history by user', function (): void {
    $paginator = new LengthAwarePaginator([], 0, 15);

    $repository = Mockery::mock(IUserQuizAttemptRepository::class);
    $repository->shouldReceive('paginateByUserId')
        ->once()
        ->with(10, Mockery::type(QueryOption::class))
        ->andReturn($paginator);

    $service = new UserQuizAttemptService($repository);

    $result = $service->historyByUser(10, new QueryOption(1, 15));

    expect($result)->toBe($paginator);
});

it('returns attempt history by quiz', function (): void {
    $paginator = new LengthAwarePaginator([], 0, 15);

    $repository = Mockery::mock(IUserQuizAttemptRepository::class);
    $repository->shouldReceive('paginateByQuizId')
        ->once()
        ->with(10, Mockery::type(QueryOption::class))
        ->andReturn($paginator);

    $service = new UserQuizAttemptService($repository);

    $result = $service->historyByQuiz(10, new QueryOption(1, 15));

    expect($result)->toBe($paginator);
});

it('returns latest attempt by user and quiz', function (): void {
    $attempt = new UserQuizAttempt;

    $repository = Mockery::mock(IUserQuizAttemptRepository::class);
    $repository->shouldReceive('latestByUserAndQuiz')
        ->once()
        ->with(10, 20)
        ->andReturn($attempt);

    $service = new UserQuizAttemptService($repository);

    $result = $service->latestAttempt(10, 20);

    expect($result)->toBe($attempt);
});
