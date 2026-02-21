<?php

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserQuizAttempt;
use App\Repositories\UserQuizAttempt\IUserQuizAttemptRepository;
use App\Repositories\UserQuizAttempt\UserQuizAttemptRepository;
use App\ValueObjects\QueryOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

it('implements shared repository contract', function (): void {
    $model = new UserQuizAttempt;
    $repository = new UserQuizAttemptRepository($model);

    assertRepositoryContract($repository, IUserQuizAttemptRepository::class, $model);
});

it('paginates attempts by user', function (): void {
    $user = User::factory()->create();
    $quiz = createQuiz();

    UserQuizAttempt::query()->create([
        'user_id' => $user->id,
        'quiz_id' => $quiz->id,
        'score_percent' => 80,
        'passed' => true,
        'submitted_at' => now(),
    ]);

    $repository = new UserQuizAttemptRepository(new UserQuizAttempt);
    $options = QueryOption::fromArray(['page' => 1, 'per_page' => 10], true);

    $paginator = $repository->paginateByUserId($user->id, $options);

    expect($paginator->total())->toBe(1);
});

it('paginates attempts by quiz', function (): void {
    $user = User::factory()->create();
    $quiz = createQuiz();

    UserQuizAttempt::query()->create([
        'user_id' => $user->id,
        'quiz_id' => $quiz->id,
        'score_percent' => 60,
        'passed' => false,
        'submitted_at' => now(),
    ]);

    $repository = new UserQuizAttemptRepository(new UserQuizAttempt);
    $options = QueryOption::fromArray(['page' => 1, 'per_page' => 10], true);

    $paginator = $repository->paginateByQuizId($quiz->id, $options);

    expect($paginator->total())->toBe(1);
});

it('returns latest attempt by user and quiz', function (): void {
    $user = User::factory()->create();
    $quiz = createQuiz();

    UserQuizAttempt::query()->create([
        'user_id' => $user->id,
        'quiz_id' => $quiz->id,
        'score_percent' => 40,
        'passed' => false,
        'submitted_at' => now()->subDay(),
    ]);

    $latest = UserQuizAttempt::query()->create([
        'user_id' => $user->id,
        'quiz_id' => $quiz->id,
        'score_percent' => 90,
        'passed' => true,
        'submitted_at' => now(),
    ]);

    $repository = new UserQuizAttemptRepository(new UserQuizAttempt);

    $found = $repository->latestByUserAndQuiz($user->id, $quiz->id);

    expect($found?->id)->toBe($latest->id);
});

function createQuiz(): Quiz
{
    $course = Course::factory()->create();
    $lesson = Lesson::query()->create([
        'name' => 'Sample Lesson',
        'slug' => 'sample-lesson',
        'course_id' => $course->id,
        'video_url' => 'https://example.com/video.mp4',
        'star_reward_video' => 0,
        'star_reward_quiz' => 0,
        'has_quiz' => true,
    ]);

    return Quiz::query()->create([
        'lesson_id' => $lesson->id,
        'pass_percent' => 80,
    ]);
}
