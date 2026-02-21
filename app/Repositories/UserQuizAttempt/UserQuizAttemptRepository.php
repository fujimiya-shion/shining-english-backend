<?php

namespace App\Repositories\UserQuizAttempt;

use App\Models\UserQuizAttempt;
use App\Repositories\Repository;
use App\ValueObjects\QueryOption;
use Illuminate\Pagination\LengthAwarePaginator;

class UserQuizAttemptRepository extends Repository implements IUserQuizAttemptRepository
{
    public function __construct(UserQuizAttempt $model)
    {
        parent::__construct($model);
    }

    public function paginateByUserId(int $userId, QueryOption $options): LengthAwarePaginator
    {
        return $this->paginateBy(['user_id' => $userId], $options);
    }

    public function paginateByQuizId(int $quizId, QueryOption $options): LengthAwarePaginator
    {
        return $this->paginateBy(['quiz_id' => $quizId], $options);
    }

    public function latestByUserAndQuiz(int $userId, int $quizId): ?UserQuizAttempt
    {
        return $this->model
            ->newQuery()
            ->where('user_id', $userId)
            ->where('quiz_id', $quizId)
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->first();
    }
}
