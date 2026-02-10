<?php

namespace App\Services\Lesson;

use App\Repositories\Lesson\ILessonRepository;
use App\Services\Service;

class LessonService extends Service implements ILessonService
{
    public function __construct(ILessonRepository $repository)
    {
        parent::__construct($repository);
    }
}
