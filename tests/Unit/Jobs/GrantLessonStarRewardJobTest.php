<?php

use App\Enums\StarTransactionType;
use App\Jobs\GrantLessonStarRewardJob;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonStarReward;
use App\Models\User;
use App\Services\Star\IStarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

it('grants lesson video stars only once for the same user and lesson', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create();
    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'star_reward_video' => 3,
        'star_reward_quiz' => 0,
    ]);

    $starService = Mockery::mock(IStarService::class);
    $starService->shouldReceive('addStarByUserId')
        ->once()
        ->with(3, $user->id, Mockery::type('string'), StarTransactionType::LessonRewardVideo)
        ->andReturnTrue();
    app()->instance(IStarService::class, $starService);

    $job = new GrantLessonStarRewardJob(
        userId: $user->id,
        courseId: $course->id,
        lessonId: $lesson->id,
        source: GrantLessonStarRewardJob::SOURCE_VIDEO,
    );

    $job->handle($starService);
    $job->handle($starService);

    expect(LessonStarReward::query()->count())->toBe(1);
    expect(LessonStarReward::query()->first()?->amount)->toBe(3);
});

it('skips granting reward when lesson config is zero', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create();
    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'star_reward_video' => 0,
        'star_reward_quiz' => 0,
    ]);

    $starService = Mockery::mock(IStarService::class);
    $starService->shouldReceive('addStarByUserId')->never();
    app()->instance(IStarService::class, $starService);

    $job = new GrantLessonStarRewardJob(
        userId: $user->id,
        courseId: $course->id,
        lessonId: $lesson->id,
        source: GrantLessonStarRewardJob::SOURCE_VIDEO,
    );

    $job->handle($starService);

    expect(LessonStarReward::query()->count())->toBe(0);
});

it('grants quiz stars with the quiz reward config', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create();
    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'star_reward_video' => 0,
        'star_reward_quiz' => 5,
    ]);

    $starService = Mockery::mock(IStarService::class);
    $starService->shouldReceive('addStarByUserId')
        ->once()
        ->with(5, $user->id, Mockery::type('string'), StarTransactionType::LessonRewardQuiz)
        ->andReturnTrue();
    app()->instance(IStarService::class, $starService);

    $job = new GrantLessonStarRewardJob(
        userId: $user->id,
        courseId: $course->id,
        lessonId: $lesson->id,
        source: GrantLessonStarRewardJob::SOURCE_QUIZ,
    );

    $job->handle($starService);

    expect(LessonStarReward::query()->count())->toBe(1);
    expect(LessonStarReward::query()->first()?->source)->toBe(GrantLessonStarRewardJob::SOURCE_QUIZ);
});

it('skips granting reward when lesson is missing or course mismatches', function (): void {
    $course = Course::factory()->create();
    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'star_reward_video' => 3,
    ]);
    $starService = Mockery::mock(IStarService::class);
    $starService->shouldReceive('addStarByUserId')->never();

    (new GrantLessonStarRewardJob(1, $course->id + 1, $lesson->id, GrantLessonStarRewardJob::SOURCE_VIDEO))->handle($starService);
    (new GrantLessonStarRewardJob(1, $course->id, 999999, GrantLessonStarRewardJob::SOURCE_VIDEO))->handle($starService);

    expect(LessonStarReward::query()->count())->toBe(0);
});

it('throws when star service cannot grant reward and logs failed jobs', function (): void {
    $course = Course::factory()->create();
    $lesson = Lesson::factory()->create([
        'course_id' => $course->id,
        'star_reward_video' => 3,
    ]);

    $starService = Mockery::mock(IStarService::class);
    $starService->shouldReceive('addStarByUserId')->once()->andReturnFalse();

    $job = new GrantLessonStarRewardJob(1, $course->id, $lesson->id, GrantLessonStarRewardJob::SOURCE_VIDEO);

    expect(fn () => $job->handle($starService))->toThrow(RuntimeException::class);

    Log::shouldReceive('error')
        ->once()
        ->with('Failed to grant lesson star reward.', Mockery::on(
            fn (array $context): bool => $context['lesson_id'] === $lesson->id
                && $context['error'] === 'failed'
        ));
    $job->failed(new RuntimeException('failed'));
});
