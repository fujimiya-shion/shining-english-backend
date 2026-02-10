<?php

use App\Models\Lesson;
use App\Repositories\Lesson\ILessonRepository;
use App\Repositories\Lesson\LessonRepository;
use Tests\TestCase;

uses(TestCase::class);

it('implements lesson repository contract', function (): void {
    $model = new Lesson;
    $repository = new LessonRepository($model);

    assertRepositoryContract($repository, ILessonRepository::class, $model);
});
