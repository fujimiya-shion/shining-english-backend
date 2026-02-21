<?php

namespace App\Services\Enrollment;

use App\Models\Enrollment;
use App\Repositories\Enrollment\IEnrollmentRepository;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

class EnrollmentService extends Service implements IEnrollmentService
{
    protected IEnrollmentRepository $enrollmentRepository;

    public function __construct(IEnrollmentRepository $repository)
    {
        parent::__construct($repository);
        $this->enrollmentRepository = $repository;
    }

    public function enroll(int $userId, int $courseId, ?int $orderId = null): Enrollment
    {
        $existing = $this->enrollmentRepository->findByUserAndCourse($userId, $courseId);

        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($userId, $courseId, $orderId): Enrollment {
            return $this->enrollmentRepository->create([
                'user_id' => $userId,
                'course_id' => $courseId,
                'order_id' => $orderId,
                'enrolled_at' => now(),
            ]);
        });
    }

    public function isEnrolled(int $userId, int $courseId): bool
    {
        return (bool) $this->enrollmentRepository->findByUserAndCourse($userId, $courseId);
    }
}
