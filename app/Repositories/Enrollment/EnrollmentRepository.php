<?php

namespace App\Repositories\Enrollment;

use App\Models\Enrollment;
use App\Repositories\Repository;

class EnrollmentRepository extends Repository implements IEnrollmentRepository
{
    public function __construct(Enrollment $model)
    {
        parent::__construct($model);
    }

    public function findByUserAndCourse(int $userId, int $courseId): ?Enrollment
    {
        return $this->model
            ->newQuery()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();
    }

    public function findByUserAndCourseWithTrashed(int $userId, int $courseId): ?Enrollment
    {
        return $this->model
            ->newQuery()
            ->withTrashed()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();
    }
}
