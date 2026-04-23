<?php

namespace App\Services\Enrollment;

use App\Enums\OrderStatus;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Repositories\Enrollment\IEnrollmentRepository;
use App\Services\Service;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
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
        $enrollment = $this->enrollmentRepository->findByUserAndCourse($userId, $courseId);

        if (! $enrollment) {
            return false;
        }

        if (! $enrollment->order_id) {
            return true;
        }

        return $enrollment->order?->status === OrderStatus::Paid;
    }

    public function hasPendingEnrollment(int $userId, int $courseId): bool
    {
        $enrollment = $this->enrollmentRepository->findByUserAndCourse($userId, $courseId);

        if (! $enrollment || ! $enrollment->order_id) {
            return false;
        }

        return $enrollment->order?->status === OrderStatus::Pending;
    }

    public function getLearningProgress(int $userId, int $courseId): ?array
    {
        $enrollment = $this->enrollmentRepository->findByUserAndCourse($userId, $courseId);
        if (! $enrollment) {
            return null;
        }

        $orderedLessons = $this->getOrderedLessons($courseId);
        $orderedLessonIds = $orderedLessons->pluck('id')->map(fn (mixed $id): int => (int) $id)->all();
        $completedLessonIds = $this->normalizeCompletedLessonIds(
            $enrollment->completed_lesson_ids,
            $orderedLessonIds,
        );

        $currentLessonId = $this->resolveCurrentLessonId(
            $orderedLessonIds,
            $completedLessonIds,
            $enrollment->current_lesson_id,
        );

        if ($currentLessonId !== $enrollment->current_lesson_id || $completedLessonIds !== ($enrollment->completed_lesson_ids ?? [])) {
            $enrollment->fill([
                'current_lesson_id' => $currentLessonId,
                'completed_lesson_ids' => $completedLessonIds,
            ])->save();
        }

        return $this->buildProgressPayload($courseId, $currentLessonId, $completedLessonIds, count($orderedLessonIds));
    }

    public function completeLesson(int $userId, int $courseId, int $lessonId): ?array
    {
        $enrollment = $this->enrollmentRepository->findByUserAndCourse($userId, $courseId);
        if (! $enrollment) {
            return null;
        }

        $orderedLessons = $this->getOrderedLessons($courseId);
        $orderedLessonIds = $orderedLessons->pluck('id')->map(fn (mixed $id): int => (int) $id)->values()->all();
        if (! in_array($lessonId, $orderedLessonIds, true)) {
            return null;
        }

        $completedLessonIds = $this->normalizeCompletedLessonIds(
            $enrollment->completed_lesson_ids,
            $orderedLessonIds,
        );

        if (! in_array($lessonId, $completedLessonIds, true)) {
            $completedLessonIds[] = $lessonId;
        }

        $completedSet = array_fill_keys($completedLessonIds, true);
        $completedInOrder = [];
        foreach ($orderedLessonIds as $orderedLessonId) {
            if (isset($completedSet[$orderedLessonId])) {
                $completedInOrder[] = $orderedLessonId;
            }
        }

        $nextLesson = null;
        foreach ($orderedLessons as $lesson) {
            $candidateId = (int) $lesson->id;
            if (! in_array($candidateId, $completedInOrder, true)) {
                $nextLesson = $lesson;
                break;
            }
        }

        $currentLessonId = $nextLesson ? (int) $nextLesson->id : (count($orderedLessonIds) > 0 ? end($orderedLessonIds) : null);

        $enrollment->fill([
            'current_lesson_id' => $currentLessonId,
            'completed_lesson_ids' => $completedInOrder,
        ])->save();

        return [
            ...$this->buildProgressPayload($courseId, $currentLessonId, $completedInOrder, count($orderedLessonIds)),
            'next_lesson' => $nextLesson
                ? [
                    'id' => (int) $nextLesson->id,
                    'has_quiz' => (bool) $nextLesson->has_quiz,
                ]
                : null,
        ];
    }

    public function setCurrentLesson(int $userId, int $courseId, int $lessonId): ?array
    {
        $enrollment = $this->enrollmentRepository->findByUserAndCourse($userId, $courseId);
        if (! $enrollment) {
            return null;
        }

        $orderedLessons = $this->getOrderedLessons($courseId);
        $orderedLessonIds = $orderedLessons->pluck('id')->map(fn (mixed $id): int => (int) $id)->all();
        if (! in_array($lessonId, $orderedLessonIds, true)) {
            return null;
        }

        $completedLessonIds = $this->normalizeCompletedLessonIds(
            $enrollment->completed_lesson_ids,
            $orderedLessonIds,
        );

        $enrollment->fill([
            'current_lesson_id' => $lessonId,
            'completed_lesson_ids' => $completedLessonIds,
        ])->save();

        return $this->buildProgressPayload($courseId, $lessonId, $completedLessonIds, count($orderedLessonIds));
    }

    /**
     * @return Collection<int, Lesson>
     */
    private function getOrderedLessons(int $courseId): Collection
    {
        return Lesson::query()
            ->where('course_id', $courseId)
            ->orderBy('group_name')
            ->orderBy('id')
            ->get(['id', 'has_quiz']);
    }

    /**
     * @param  list<int>  $orderedLessonIds
     * @return list<int>
     */
    private function normalizeCompletedLessonIds(mixed $rawCompletedLessonIds, array $orderedLessonIds): array
    {
        if (! is_array($rawCompletedLessonIds) || $orderedLessonIds === []) {
            return [];
        }

        $allowedSet = array_fill_keys($orderedLessonIds, true);
        $normalized = [];

        foreach ($rawCompletedLessonIds as $value) {
            $lessonId = is_numeric($value) ? (int) $value : null;
            if ($lessonId === null || ! isset($allowedSet[$lessonId])) {
                continue;
            }

            if (! in_array($lessonId, $normalized, true)) {
                $normalized[] = $lessonId;
            }
        }

        $result = [];
        foreach ($orderedLessonIds as $lessonId) {
            if (in_array($lessonId, $normalized, true)) {
                $result[] = $lessonId;
            }
        }

        return $result;
    }

    /**
     * @param  list<int>  $orderedLessonIds
     * @param  list<int>  $completedLessonIds
     */
    private function resolveCurrentLessonId(array $orderedLessonIds, array $completedLessonIds, mixed $storedCurrentLessonId): ?int
    {
        if ($orderedLessonIds === []) {
            return null;
        }

        $candidate = is_numeric($storedCurrentLessonId) ? (int) $storedCurrentLessonId : null;
        if ($candidate !== null && in_array($candidate, $orderedLessonIds, true)) {
            return $candidate;
        }

        $completedSet = array_fill_keys($completedLessonIds, true);
        foreach ($orderedLessonIds as $lessonId) {
            if (! isset($completedSet[$lessonId])) {
                return $lessonId;
            }
        }

        return end($orderedLessonIds) ?: null;
    }

    /**
     * @param  list<int>  $completedLessonIds
     * @return array{
     *   course_id:int,
     *   current_lesson_id:int|null,
     *   completed_lesson_ids:list<int>,
     *   total_lessons:int,
     *   progress_percentage:float
     * }
     */
    private function buildProgressPayload(int $courseId, ?int $currentLessonId, array $completedLessonIds, int $totalLessons): array
    {
        $progressPercentage = $totalLessons > 0
            ? round((count($completedLessonIds) / $totalLessons) * 100, 2)
            : 0.0;

        return [
            'course_id' => $courseId,
            'current_lesson_id' => $currentLessonId,
            'completed_lesson_ids' => $completedLessonIds,
            'total_lessons' => $totalLessons,
            'progress_percentage' => $progressPercentage,
        ];
    }
}
