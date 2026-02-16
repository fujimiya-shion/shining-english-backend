<?php
namespace App\Services\Course;

use App\Repositories\Course\ICourseRepository;
use App\Services\Service;
use App\ValueObjects\CourseFilter;
use Illuminate\Pagination\LengthAwarePaginator;
class CourseService extends Service implements ICourseService {
    protected ICourseRepository $courseRepository;
    public function __construct(ICourseRepository $repository) {
        parent::__construct($repository);
        $this->courseRepository = $repository;
    }

    public function filter(CourseFilter $filters): LengthAwarePaginator
    {
        return $this->courseRepository->filter($filters);
    }
}
