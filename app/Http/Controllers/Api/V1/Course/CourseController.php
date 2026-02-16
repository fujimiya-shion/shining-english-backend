<?php

namespace App\Http\Controllers\Api\V1\Course;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Course\CourseFilterRequest;
use App\Services\Course\ICourseService;
use App\Services\IService;
use App\Traits\ApiBehaviour;
use App\ValueObjects\MetaPagination;
use App\ValueObjects\CourseFilter;
use Illuminate\Http\JsonResponse;

class CourseController extends ApiController
{
    use ApiBehaviour;

    public function __construct(
        protected ICourseService $service
    ) {}

    protected function service(): IService
    {
        return $this->service;
    }

    public function filter(CourseFilterRequest $request): JsonResponse {
        $filters = CourseFilter::fromArray($request->validated());
        $paginator = $this->service->filter($filters);
        $collections = $paginator->getCollection();
        $meta = MetaPagination::fromLengthAwarePaginator($paginator);

        return $this->success(
            data: $collections,
            meta: $meta->toArray(),
        );
    }
}
