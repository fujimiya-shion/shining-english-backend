<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Repositories\Enrollment\EnrollmentRepository;
use App\Repositories\Enrollment\IEnrollmentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

it('implements shared repository contract', function (): void {
    $model = new Enrollment;
    $repository = new EnrollmentRepository($model);

    assertRepositoryContract($repository, IEnrollmentRepository::class, $model);
});

it('finds enrollment by user and course', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create();
    $enrollment = Enrollment::query()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'order_id' => null,
        'enrolled_at' => now(),
    ]);

    $repository = new EnrollmentRepository(new Enrollment);

    $found = $repository->findByUserAndCourse($user->id, $course->id);

    expect($found?->id)->toBe($enrollment->id);
});

it('finds enrollment including soft deleted records', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create();
    $enrollment = Enrollment::query()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'order_id' => null,
        'enrolled_at' => now(),
    ]);
    $enrollment->delete();

    $repository = new EnrollmentRepository(new Enrollment);

    $found = $repository->findByUserAndCourseWithTrashed($user->id, $course->id);

    expect($found?->id)->toBe($enrollment->id);
    expect($found?->trashed())->toBeTrue();
});
