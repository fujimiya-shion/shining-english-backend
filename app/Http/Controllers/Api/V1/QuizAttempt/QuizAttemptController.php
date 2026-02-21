<?php

namespace App\Http\Controllers\Api\V1\QuizAttempt;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\QuizAttempt\QuizAttemptStoreRequest;
use App\Services\UserQuizAttempt\IUserQuizAttemptService;
use App\Traits\Jsonable;
use App\ValueObjects\MetaPagination;
use App\ValueObjects\QueryOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class QuizAttemptController extends ApiController
{
    use Jsonable;

    public function __construct(
        protected IUserQuizAttemptService $service
    ) {}

    public function index(Request $request, int $quizId): JsonResponse
    {
        $user = $request->user();
        $options = QueryOption::fromArray($request->all(), true);

        $paginator = $this->service->paginateBy([
            'user_id' => $user->id,
            'quiz_id' => $quizId,
        ], $options);

        $meta = MetaPagination::fromLengthAwarePaginator($paginator);

        return $this->success(
            data: $paginator->getCollection(),
            meta: $meta->toArray(),
        );
    }

    public function store(QuizAttemptStoreRequest $request, int $quizId): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $submittedAt = isset($data['submitted_at'])
            ? Carbon::parse($data['submitted_at'])
            : null;

        $attempt = $this->service->recordAttempt(
            $user->id,
            $quizId,
            (float) $data['score_percent'],
            (bool) $data['passed'],
            $submittedAt,
        );

        return $this->created($attempt, 'Attempt recorded');
    }

    public function latest(Request $request, int $quizId): JsonResponse
    {
        $user = $request->user();
        $attempt = $this->service->latestAttempt($user->id, $quizId);

        if (! $attempt) {
            return $this->notfound('Attempt not found');
        }

        return $this->success(data: $attempt);
    }
}
