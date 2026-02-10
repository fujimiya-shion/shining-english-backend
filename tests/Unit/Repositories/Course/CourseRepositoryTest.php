<?php

use App\Models\Course;
use App\Repositories\Course\CourseRepository;
use App\Repositories\Course\ICourseRepository;

it('implements shared repository contract', function (): void {
    $model = new Course;
    $repository = new CourseRepository($model);

    assertRepositoryContract($repository, ICourseRepository::class, $model);
});
