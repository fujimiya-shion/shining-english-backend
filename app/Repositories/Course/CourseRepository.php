<?php
namespace App\Repositories\Course;

use App\Models\Course;
use App\Repositories\Repository;
class CourseRepository extends Repository implements ICourseRepository {
    public function __construct(Course $model) {
        $this->model = $model;
    }
}