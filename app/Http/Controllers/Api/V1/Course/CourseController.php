<?php

namespace App\Http\Controllers\Api\V1\Course;

use App\Http\Controllers\Api\ApiController;
use App\Services\Course\ICourseService;
use App\Services\IService;
use App\Traits\ApiBehaviour;

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
}
