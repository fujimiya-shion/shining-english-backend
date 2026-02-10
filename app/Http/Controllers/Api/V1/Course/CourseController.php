<?php

namespace App\Http\Controllers\Api\V1\Course;

use App\Http\Controllers\Api\ApiController;
use App\Services\Course\ICourseService;
use Illuminate\Http\Request;
use Jsonable;

class CourseController extends ApiController {
    use Jsonable;
    public function __construct(
        protected ICourseService $service
    ) {}

    public function index(Request $request) {

    }
}
