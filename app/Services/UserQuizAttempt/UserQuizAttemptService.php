<?php

namespace App\Services\UserQuizAttempt;

use App\Models\UserQuizAttempt;
use App\Repositories\UserQuizAttempt\IUserQuizAttemptRepository;
use App\Services\Service;
use App\ValueObjects\QueryOption;
use Illuminate\Pagination\LengthAwarePaginator;

class UserQuizAttemptService extends Service implements IUserQuizAttemptService
{
    protected IUserQuizAttemptRepository $attemptRepository;

    public function __construct(IUserQuizAttemptRepository $repository)
    {
        parent::__construct($repository);
        $this->attemptRepository = $repository;
    }

    public function recordAttempt(
        int $userId,
        int $quizId,
        float $scorePercent,
        bool $passed,
        ?\DateTimeInterface $submittedAt = null,
    ): UserQuizAttempt {
        return $this->attemptRepository->create([
            'user_id' => $userId,
            'quiz_id' => $quizId,
            'score_percent' => $scorePercent,
            'passed' => $passed,
            'submitted_at' => $submittedAt ?? now(),
        ]);
    }

    public function historyByUser(int $userId, QueryOption $options): LengthAwarePaginator
    {
        return $this->attemptRepository->paginateByUserId($userId, $options);
    }

    public function historyByQuiz(int $quizId, QueryOption $options): LengthAwarePaginator
    {
        return $this->attemptRepository->paginateByQuizId($quizId, $options);
    }

    public function latestAttempt(int $userId, int $quizId): ?UserQuizAttempt
    {
        return $this->attemptRepository->latestByUserAndQuiz($userId, $quizId);
    }
}
