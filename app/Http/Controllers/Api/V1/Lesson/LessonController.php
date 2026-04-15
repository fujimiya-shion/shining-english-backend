<?php

namespace App\Http\Controllers\Api\V1\Lesson;

use App\Http\Controllers\Api\ApiController;
use App\Services\IService;
use App\Services\Lesson\ILessonService;
use App\Traits\ApiBehaviour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function quiz(Request $request): JsonResponse
    {
        $id = (int) $request->route('id');
        $lesson = $this->service->getById($id);

        if (! $lesson) {
            return $this->notfound();
        }

        $quiz = $lesson->quiz()
            ->with(['questions.answers'])
            ->first();

        if (! $quiz) {
            return $this->notfound();
        }

        return $this->success('Get Quiz Successfully', $quiz);
    }

    public function downloadDocument(int $id, int $documentIndex): JsonResponse|StreamedResponse
    {
        $lesson = $this->service->getById($id);

        if (! $lesson) {
            return $this->notfound();
        }

        $paths = collect($lesson->documents ?? [])
            ->filter(fn (mixed $value): bool => is_string($value) && trim($value) !== '')
            ->values();

        $path = $paths->get($documentIndex);
        if (! is_string($path) || ! Storage::disk('local')->exists($path)) {
            return $this->notfound();
        }

        $fileName = collect($lesson->document_names ?? [])->values()->get($documentIndex);
        if (! is_string($fileName) || trim($fileName) === '') {
            $fileName = basename($path);
        }

        return Storage::disk('local')->download($path, $fileName);
    }
}
