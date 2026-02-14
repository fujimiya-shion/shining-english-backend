<?php

namespace App\Repositories\Quiz;

use App\Models\Quiz;
use App\Repositories\Repository;

class QuizRepository extends Repository implements IQuizRepository
{
    public function __construct(Quiz $model)
    {
        parent::__construct($model);
    }
}
