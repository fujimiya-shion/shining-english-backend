<?php

namespace App\Http\Controllers\Api\V1\Lesson;

use App\Http\Controllers\Api\ApiController;
use App\Services\IService;
use App\Services\Lesson\ILessonService;
use App\Traits\ApiBehaviour;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonController extends ApiController
{
    use ApiBehaviour;

    public function __construct(
        protected ILessonService $service,
    ) {}

    protected function service(): IService
    {
        return $this->service;
    }

    public function quiz(Request $request): JsonResponse {
        $id = (int) $request->route('id');
        $lesson = $this->service->getById($id);
        if(!$lesson)
            return $this->notfound();

        /** @var Collection<int, Model> $quiz */
        $quiz = $lesson->quiz;
        $quiz->load(['questions.answers']);
        if(!$quiz)
            return $this->notfound();
        return $this->success('Get Quiz Successfully', $quiz);
    }
}
