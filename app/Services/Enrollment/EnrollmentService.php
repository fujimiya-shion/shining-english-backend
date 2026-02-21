<?php

namespace App\Services\Enrollment;

use App\Models\Enrollment;
use App\Repositories\Enrollment\IEnrollmentRepository;
use App\Services\Service;
use Illuminate\Database\QueryException;
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
        $existing = $this->enrollmentRepository->findByUserAndCourseWithTrashed($userId, $courseId);

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
                $existing->fill([
                    'order_id' => $orderId,
                    'enrolled_at' => now(),
                ]);
                $existing->save();

                return $existing->refresh();
            }

            return $existing;
        }

        return DB::transaction(function () use ($userId, $courseId, $orderId): Enrollment {
            try {
                return $this->enrollmentRepository->create([
                    'user_id' => $userId,
                    'course_id' => $courseId,
                    'order_id' => $orderId,
                    'enrolled_at' => now(),
                ]);
            } catch (QueryException $exception) {
                $existing = $this->enrollmentRepository->findByUserAndCourse($userId, $courseId);

                if ($existing) {
                    return $existing;
                }

                throw $exception;
            }
        });
    }

    public function isEnrolled(int $userId, int $courseId): bool
    {
        return (bool) $this->enrollmentRepository->findByUserAndCourse($userId, $courseId);
    }
}
