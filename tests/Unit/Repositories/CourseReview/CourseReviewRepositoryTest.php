<?php

use App\Models\Course;
use App\Models\CourseReview;
use App\Models\User;
use App\Repositories\CourseReview\CourseReviewRepository;
use App\Repositories\CourseReview\ICourseReviewRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('implements course review repository contract and finds by course and user', function (): void {
    $model = new CourseReview;
    $repository = new CourseReviewRepository($model);
    assertRepositoryContract($repository, ICourseReviewRepository::class, $model);

    $course = Course::factory()->create();
    $user = User::factory()->create();
    $review = CourseReview::factory()->create([
        'course_id' => $course->id,
        'user_id' => $user->id,
        'rating' => 4,
    ]);

    expect($repository->findByCourseAndUser($course->id, $user->id)?->id)->toBe($review->id);
    expect($repository->findByCourseAndUser($course->id, $user->id + 1))->toBeNull();
    expect($repository->averageRatingByCourse($course->id))->toBe(4.0);
    expect($repository->averageRatingByCourse($course->id + 1))->toBeNull();
});
