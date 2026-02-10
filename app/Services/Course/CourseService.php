<?php
namespace App\Services\Course;

use App\Repositories\Course\ICourseRepository;
use App\Services\Service;
class CourseService extends Service implements ICourseService {
    public function __construct(ICourseRepository $repository) {
        $this->repository = $repository;
    }
}