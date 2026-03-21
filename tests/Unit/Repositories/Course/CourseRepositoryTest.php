<?php

use App\Models\Category;
use App\Models\Course;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Course\CourseRepository;
use App\Repositories\Course\ICourseRepository;

it('implements shared repository contract', function (): void {
    $model = new Course;
    $repository = new CourseRepository($model, new CategoryRepository(new Category));

    assertRepositoryContract($repository, ICourseRepository::class, $model);
});
