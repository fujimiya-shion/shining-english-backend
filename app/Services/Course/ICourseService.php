<?php
namespace App\Services\Course;

use App\Services\IService;
use App\ValueObjects\CourseFilter;
use Illuminate\Pagination\LengthAwarePaginator;
interface ICourseService extends IService {
    public function filter(CourseFilter $filters): LengthAwarePaginator;
}
