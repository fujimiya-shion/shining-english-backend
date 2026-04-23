<?php

namespace Tests\Unit\Services\Enrollment;

use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Enrollment\EnrollmentRepository;
use App\Repositories\Enrollment\IEnrollmentRepository;
use App\Services\Enrollment\EnrollmentService;
use App\Services\Enrollment\IEnrollmentService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use RuntimeException;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

it('implements shared service contract', function (): void {
    $model = new Enrollment;
    $repository = new EnrollmentRepository($model);
    $service = new EnrollmentService($repository);

    assertServiceContract($service, IEnrollmentService::class, $repository);
});

it('enrolls a user when missing', function (): void {
    $enrollment = new Enrollment;

    $repository = Mockery::mock(IEnrollmentRepository::class);
    $repository->shouldReceive('findByUserAndCourseWithTrashed')
        ->once()
        ->with(10, 20)
        ->andReturnNull();
    $repository->shouldReceive('create')
        ->once()
        ->with(Mockery::on(function (array $data): bool {
            return $data['user_id'] === 10
                && $data['course_id'] === 20
                && $data['order_id'] === 30
                && isset($data['enrolled_at']);
        }))
        ->andReturn($enrollment);

    $service = new EnrollmentService($repository);

    $result = $service->enroll(10, 20, 30);

    expect($result)->toBe($enrollment);
});

it('returns existing enrollment', function (): void {
    $enrollment = new Enrollment;

    $repository = Mockery::mock(IEnrollmentRepository::class);
    $repository->shouldReceive('findByUserAndCourseWithTrashed')
        ->once()
        ->with(10, 20)
        ->andReturn($enrollment);
    $repository->shouldReceive('create')->never();

    $service = new EnrollmentService($repository);

    $result = $service->enroll(10, 20);

    expect($result)->toBe($enrollment);
});

it('returns existing enrollment when create hits a duplicate', function (): void {
    $enrollment = new Enrollment;

    $repository = Mockery::mock(IEnrollmentRepository::class);
    $repository->shouldReceive('findByUserAndCourseWithTrashed')
        ->once()
        ->with(10, 20)
        ->andReturnNull();
    $repository->shouldReceive('create')
        ->once()
        ->andThrow(new QueryException(
            '',
            '',
            [],
            new RuntimeException('duplicate'),
        ));
    $repository->shouldReceive('findByUserAndCourse')
        ->once()
        ->with(10, 20)
        ->andReturn($enrollment);

    $service = new EnrollmentService($repository);

    $result = $service->enroll(10, 20);

    expect($result)->toBe($enrollment);
});

it('throws when duplicate occurs without existing enrollment', function (): void {
    $repository = Mockery::mock(IEnrollmentRepository::class);
    $repository->shouldReceive('findByUserAndCourseWithTrashed')
        ->once()
        ->with(10, 20)
        ->andReturnNull();
    $repository->shouldReceive('create')
        ->once()
        ->andThrow(new QueryException(
            '',
            '',
            [],
            new RuntimeException('duplicate'),
        ));
    $repository->shouldReceive('findByUserAndCourse')
        ->once()
        ->with(10, 20)
        ->andReturnNull();

    $service = new EnrollmentService($repository);

    expect(fn () => $service->enroll(10, 20))->toThrow(QueryException::class);
});

it('restores a soft deleted enrollment', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create();

    /** @var Enrollment $enrollment */
    $enrollment = Enrollment::query()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'order_id' => null,
        'enrolled_at' => now()->subDays(2),
    ]);

    $enrollment->delete();

    $repository = new EnrollmentRepository(new Enrollment);
    $service = new EnrollmentService($repository);

    $result = $service->enroll($user->id, $course->id, 123);

    expect($result->trashed())->toBeFalse();
    expect($result->order_id)->toBe(123);
});

it('checks enrollment status', function (): void {
    $enrollment = new Enrollment;
    $enrollment->order_id = null;

    $repository = Mockery::mock(IEnrollmentRepository::class);
    $repository->shouldReceive('findByUserAndCourse')
        ->once()
        ->with(10, 20)
        ->andReturn($enrollment);

    $service = new EnrollmentService($repository);

    expect($service->isEnrolled(10, 20))->toBeTrue();
});

it('returns false when enrollment does not exist', function (): void {
    $repository = Mockery::mock(IEnrollmentRepository::class);
    $repository->shouldReceive('findByUserAndCourse')
        ->once()
        ->with(10, 20)
        ->andReturnNull();

    $service = new EnrollmentService($repository);

    expect($service->isEnrolled(10, 20))->toBeFalse();
});

it('returns false when enrollment order is not paid', function (): void {
    $enrollment = new Enrollment;
    $enrollment->order_id = 30;
    $enrollment->setRelation('order', new Order([
        'status' => OrderStatus::Pending,
    ]));

    $repository = Mockery::mock(IEnrollmentRepository::class);
    $repository->shouldReceive('findByUserAndCourse')
        ->once()
        ->with(10, 20)
        ->andReturn($enrollment);

    $service = new EnrollmentService($repository);

    expect($service->isEnrolled(10, 20))->toBeFalse();
});

it('returns true when enrollment order is paid', function (): void {
    $enrollment = new Enrollment;
    $enrollment->order_id = 30;
    $enrollment->setRelation('order', new Order([
        'status' => OrderStatus::Paid,
    ]));

    $repository = Mockery::mock(IEnrollmentRepository::class);
    $repository->shouldReceive('findByUserAndCourse')
        ->once()
        ->with(10, 20)
        ->andReturn($enrollment);

    $service = new EnrollmentService($repository);

    expect($service->isEnrolled(10, 20))->toBeTrue();
});

it('returns true when enrollment order is pending approval', function (): void {
    $enrollment = new Enrollment;
    $enrollment->order_id = 30;
    $enrollment->setRelation('order', new Order([
        'status' => OrderStatus::Pending,
    ]));

    $repository = Mockery::mock(IEnrollmentRepository::class);
    $repository->shouldReceive('findByUserAndCourse')
        ->once()
        ->with(10, 20)
        ->andReturn($enrollment);

    $service = new EnrollmentService($repository);

    expect($service->hasPendingEnrollment(10, 20))->toBeTrue();
});

it('returns false when enrollment does not exist while checking pending approval', function (): void {
    $repository = Mockery::mock(IEnrollmentRepository::class);
    $repository->shouldReceive('findByUserAndCourse')
        ->once()
        ->with(10, 20)
        ->andReturnNull();

    $service = new EnrollmentService($repository);

    expect($service->hasPendingEnrollment(10, 20))->toBeFalse();
});

it('returns false when enrollment has no order while checking pending approval', function (): void {
    $enrollment = new Enrollment;
    $enrollment->order_id = null;

    $repository = Mockery::mock(IEnrollmentRepository::class);
    $repository->shouldReceive('findByUserAndCourse')
        ->once()
        ->with(10, 20)
        ->andReturn($enrollment);

    $service = new EnrollmentService($repository);

    expect($service->hasPendingEnrollment(10, 20))->toBeFalse();
});

it('returns false when enrollment order is already paid while checking pending approval', function (): void {
    $enrollment = new Enrollment;
    $enrollment->order_id = 30;
    $enrollment->setRelation('order', new Order([
        'status' => OrderStatus::Paid,
    ]));

    $repository = Mockery::mock(IEnrollmentRepository::class);
    $repository->shouldReceive('findByUserAndCourse')
        ->once()
        ->with(10, 20)
        ->andReturn($enrollment);

    $service = new EnrollmentService($repository);

    expect($service->hasPendingEnrollment(10, 20))->toBeFalse();
});

it('returns persisted learning progress payload for enrollment', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create();
    $lessonA = Lesson::query()->create([
        'name' => 'A',
        'course_id' => $course->id,
        'group_name' => 'M1',
        'video_url' => 'lessons/a.mp4',
        'duration_minutes' => 5,
        'has_quiz' => false,
    ]);
    $lessonB = Lesson::query()->create([
        'name' => 'B',
        'course_id' => $course->id,
        'group_name' => 'M1',
        'video_url' => 'lessons/b.mp4',
        'duration_minutes' => 6,
        'has_quiz' => true,
    ]);

    Enrollment::query()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'enrolled_at' => now(),
    ]);
    LessonProgress::query()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'lesson_id' => $lessonA->id,
        'completed_at' => now()->subMinute(),
        'is_current' => false,
    ]);
    LessonProgress::query()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'lesson_id' => $lessonB->id,
        'completed_at' => null,
        'is_current' => true,
    ]);

    $service = new EnrollmentService(new EnrollmentRepository(new Enrollment));
    $result = $service->getLearningProgress($user->id, $course->id);

    expect($result)->not->toBeNull();
    expect($result['current_lesson_id'])->toBe($lessonB->id);
    expect($result['completed_lesson_ids'])->toBe([$lessonA->id]);
    expect($result['total_lessons'])->toBe(2);
    expect($result['progress_percentage'])->toBe(50.0);
});

it('completes a lesson, moves to next lesson and returns next quiz hint', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create();
    $lessonA = Lesson::query()->create([
        'name' => 'A',
        'course_id' => $course->id,
        'group_name' => 'M1',
        'video_url' => 'lessons/a.mp4',
        'duration_minutes' => 5,
        'has_quiz' => false,
    ]);
    $lessonB = Lesson::query()->create([
        'name' => 'B',
        'course_id' => $course->id,
        'group_name' => 'M1',
        'video_url' => 'lessons/b.mp4',
        'duration_minutes' => 6,
        'has_quiz' => true,
    ]);

    Enrollment::query()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'enrolled_at' => now(),
    ]);
    LessonProgress::query()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'lesson_id' => $lessonA->id,
        'completed_at' => null,
        'is_current' => true,
    ]);

    $service = new EnrollmentService(new EnrollmentRepository(new Enrollment));
    $result = $service->completeLesson($user->id, $course->id, $lessonA->id);

    expect($result)->not->toBeNull();
    expect($result['current_lesson_id'])->toBe($lessonB->id);
    expect($result['completed_lesson_ids'])->toBe([$lessonA->id]);
    expect($result['next_lesson'])->toBe([
        'id' => $lessonB->id,
        'has_quiz' => true,
    ]);
});
