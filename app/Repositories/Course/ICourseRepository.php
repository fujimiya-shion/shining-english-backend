<?php
namespace App\Repositories\Course;

use App\Repositories\IRepository;
use App\ValueObjects\CourseFilter;
use Illuminate\Pagination\LengthAwarePaginator;
interface ICourseRepository extends IRepository {
    public function filter(CourseFilter $filters): LengthAwarePaginator;
}
