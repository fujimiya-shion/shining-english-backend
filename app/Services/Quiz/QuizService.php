<?php

namespace App\Services\Quiz;

use App\Repositories\Quiz\IQuizRepository;
use App\Services\Service;

class QuizService extends Service implements IQuizService
{
    public function __construct(IQuizRepository $repository)
    {
        parent::__construct($repository);
    }
}
