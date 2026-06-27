<?php

use App\Models\LessonComment;
use App\Repositories\LessonComment\ILessonCommentRepository;
use App\Repositories\LessonComment\LessonCommentRepository;

it('implements lesson comment repository contract', function (): void {
    $model = new LessonComment;
    $repository = new LessonCommentRepository($model);

    assertRepositoryContract($repository, ILessonCommentRepository::class, $model);
});
