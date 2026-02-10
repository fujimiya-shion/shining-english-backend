<?php

namespace App\Repositories\Lesson;

use App\Models\Lesson;
use App\Repositories\Repository;

class LessonRepository extends Repository implements ILessonRepository
{
    public function __construct(Lesson $model)
    {
        parent::__construct($model);
    }
}
