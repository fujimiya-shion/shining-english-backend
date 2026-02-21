<?php

namespace App\Services\UserQuizAttempt;

use App\Models\UserQuizAttempt;
use App\Services\IService;
use App\ValueObjects\QueryOption;
use Illuminate\Pagination\LengthAwarePaginator;

interface IUserQuizAttemptService extends IService
{
    public function recordAttempt(
        int $userId,
        int $quizId,
        float $scorePercent,
        bool $passed,
        ?\DateTimeInterface $submittedAt = null,
    ): UserQuizAttempt;

    public function historyByUser(int $userId, QueryOption $options): LengthAwarePaginator;

    public function historyByQuiz(int $quizId, QueryOption $options): LengthAwarePaginator;

    public function latestAttempt(int $userId, int $quizId): ?UserQuizAttempt;
}
