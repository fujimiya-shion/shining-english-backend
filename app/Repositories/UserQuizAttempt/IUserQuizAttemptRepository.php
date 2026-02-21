<?php

namespace App\Repositories\UserQuizAttempt;

use App\Models\UserQuizAttempt;
use App\Repositories\IRepository;
use App\ValueObjects\QueryOption;
use Illuminate\Pagination\LengthAwarePaginator;

interface IUserQuizAttemptRepository extends IRepository
{
    public function paginateByUserId(int $userId, QueryOption $options): LengthAwarePaginator;

    public function paginateByQuizId(int $quizId, QueryOption $options): LengthAwarePaginator;

    public function latestByUserAndQuiz(int $userId, int $quizId): ?UserQuizAttempt;
}
