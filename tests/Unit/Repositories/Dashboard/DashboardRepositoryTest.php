<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserQuizAttempt;
use App\Repositories\Dashboard\DashboardRepository;
use App\Repositories\Dashboard\IDashboardRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

it('implements dashboard repository and fetches dashboard rows', function (): void {
    $model = new Enrollment;
    $repository = new DashboardRepository($model);
    assertRepositoryContract($repository, IDashboardRepository::class, $model);

    $user = User::factory()->create();
    $course = Course::factory()->create();
    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'duration_minutes' => 15,
    ]);
    $quiz = Quiz::query()->create(['lesson_id' => $lesson->id, 'pass_percent' => 70]);

    Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);
    LessonProgress::query()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'lesson_id' => $lesson->id,
        'is_current' => true,
        'completed_at' => now(),
    ]);
    UserQuizAttempt::query()->create([
        'user_id' => $user->id,
        'quiz_id' => $quiz->id,
        'score_percent' => 90,
        'passed' => true,
        'submitted_at' => now(),
    ]);

    expect($repository->getEnrollmentsByUserId($user->id))->toHaveCount(1);
    expect($repository->getLessonProgressByUserAndCourseIds($user->id, collect()))->toHaveCount(0);
    expect($repository->getLessonProgressByUserAndCourseIds($user->id, collect([$course->id])))->toHaveCount(1);
    expect($repository->getRecentQuizAttemptsByUserId($user->id, 1))->toHaveCount(1);
});
