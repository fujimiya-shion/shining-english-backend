<?php

use App\Repositories\Lesson\ILessonRepository;
use App\Services\Lesson\ILessonService;
use App\Services\Lesson\LessonService;
use Tests\TestCase;

uses(TestCase::class);

it('implements lesson service contract', function (): void {
    $repository = \Mockery::mock(ILessonRepository::class);
    $service = new LessonService($repository);

    assertServiceContract($service, ILessonService::class, $repository);
});
