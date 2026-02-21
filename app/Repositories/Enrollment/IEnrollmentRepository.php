<?php

namespace App\Repositories\Enrollment;

use App\Models\Enrollment;
use App\Repositories\IRepository;

interface IEnrollmentRepository extends IRepository
{
    public function findByUserAndCourse(int $userId, int $courseId): ?Enrollment;

    public function findByUserAndCourseWithTrashed(int $userId, int $courseId): ?Enrollment;
}
