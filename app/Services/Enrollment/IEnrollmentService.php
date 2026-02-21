<?php

namespace App\Services\Enrollment;

use App\Models\Enrollment;
use App\Services\IService;

interface IEnrollmentService extends IService
{
    public function enroll(int $userId, int $courseId, ?int $orderId = null): Enrollment;

    public function isEnrolled(int $userId, int $courseId): bool;
}
