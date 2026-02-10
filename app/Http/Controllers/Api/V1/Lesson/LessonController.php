<?php

namespace App\Http\Controllers\Api\V1\Lesson;

use App\Http\Controllers\Api\ApiController;
use App\Services\IService;
use App\Services\Lesson\ILessonService;
use App\Traits\ApiBehaviour;

class LessonController extends ApiController
{
    use ApiBehaviour;

    public function __construct(
        protected ILessonService $service,
    ) {}

    protected function service(): IService
    {
        return $this->service;
    }
}
