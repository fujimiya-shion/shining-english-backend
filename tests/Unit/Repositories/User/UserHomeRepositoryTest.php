<?php

use App\DTO\User\Page\Home\HomeResponse;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\User;
use App\Repositories\User\UserHomeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('builds complete home payloads with defaults and enrollment flags', function (): void {
    $category = Category::factory()->create();
    $level = Level::factory()->create();
    $course = Course::factory()->create([
        'category_id' => $category->id,
        'level_id' => $level->id,
        'status' => true,
        'learned' => 20,
        'rating' => 4.5,
    ]);
    Lesson::factory()->create(['course_id' => $course->id]);
    $user = User::factory()->create();
    Enrollment::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);
    CourseReview::factory()->create([
        'course_id' => $course->id,
        'user_id' => $user->id,
        'rating' => 5,
        'content' => 'Great course',
    ]);

    $token = $user->createToken('test-token')->plainTextToken;
    $response = (new UserHomeRepository)->getUserHomeData($token);
    $array = $response->toArray();

    expect($response)->toBeInstanceOf(HomeResponse::class);
    expect($array['payloads'])->toHaveCount(8);
    expect(collect($array['payloads'])->pluck('type')->all())->toBe([
        'banner',
        'hero',
        'courses',
        'feature',
        'process',
        'testimonials',
        'statistics',
        'cta',
    ]);

    $coursesPayload = collect($array['payloads'])->firstWhere('type', 'courses');
    expect($coursesPayload['data']['courses'][0]['enrolled'])->toBeTrue();
});
