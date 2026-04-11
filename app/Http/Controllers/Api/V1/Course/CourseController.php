<?php

namespace App\Http\Controllers\Api\V1\Course;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Course\CourseFilterRequest;
use App\Services\Cart\ICartService;
use App\Services\Course\ICourseService;
use App\Services\Enrollment\IEnrollmentService;
use App\Services\IService;
use App\Traits\ApiBehaviour;
use App\ValueObjects\CourseFilter;
use App\ValueObjects\MetaPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends ApiController
{
    use ApiBehaviour;

    public function __construct(
        protected ICourseService $service,
        protected ICartService $cartService,
        protected IEnrollmentService $enrollmentService,
    ) {}

    protected function service(): IService
    {
        return $this->service;
    }

    public function filter(CourseFilterRequest $request): JsonResponse
    {
        $filters = CourseFilter::fromArray($request->validated());
        $paginator = $this->service->filter($filters);
        $collections = $paginator->getCollection();
        $meta = MetaPagination::fromLengthAwarePaginator($paginator);

        return $this->success(
            data: $collections,
            meta: $meta->toArray(),
        );
    }

    public function getFilterProps(): JsonResponse
    {
        return $this->success(data: $this->service->getFilterProps());
    }

    public function showBySlug(string $slug): JsonResponse
    {
        $record = $this->service->getBySlug($slug);

        if (! $record) {
            return $this->notfound();
        }

        return $this->success(data: $record);
    }

    public function access(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $course = $this->service->getById($id);

        if (! $course) {
            return $this->notfound();
        }

        return $this->success(data: [
            'course_id' => $id,
            'enrolled' => $this->enrollmentService->isEnrolled($user->id, $id),
            'pending_access' => $this->enrollmentService->hasPendingEnrollment($user->id, $id),
            'in_cart' => $this->cartService->hasCourse($user->id, $id),
        ]);
    }
}
