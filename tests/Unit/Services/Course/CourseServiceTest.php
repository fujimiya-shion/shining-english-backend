<?php
namespace Tests\Unit\Services\Course;

use App\Models\Course;
use App\Repositories\Course\CourseRepository;
use App\Services\Course\CourseService;
use App\Services\Course\ICourseService;

it("implements shared service contract", function () {
    $model = new Course;
    $repository = new CourseRepository($model);
    $service = new CourseService($repository);
    assertServiceContract($service, ICourseService::class, $repository);
});