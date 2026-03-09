<?php

use App\Models\Category;
use App\Models\Course;
use App\Repositories\Course\CourseRepository;
use App\ValueObjects\CourseFilter;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

uses(TestCase::class);
uses(DatabaseMigrations::class);

it('filters courses by category status ranges and keyword', function (): void {
    $categoryA = Category::factory()->create();
    $categoryB = Category::factory()->create();

    Course::factory()->create([
        'category_id' => $categoryA->id,
        'name' => 'Basic English',
        'price' => 200,
        'status' => true,
        'rating' => 4.5,
        'learned' => 15,
    ]);

    Course::factory()->create([
        'category_id' => $categoryA->id,
        'name' => 'Advanced English',
        'price' => 600,
        'status' => true,
        'rating' => 4.9,
        'learned' => 30,
    ]);

    Course::factory()->create([
        'category_id' => $categoryB->id,
        'name' => 'Basic Japanese',
        'price' => 200,
        'status' => false,
        'rating' => 3.2,
        'learned' => 12,
    ]);

    $repository = new CourseRepository(new Course);

    $filters = CourseFilter::fromArray([
        'category_id' => $categoryA->id,
        'status' => true,
        'price_min' => 100,
        'price_max' => 300,
        'rating_min' => 4.0,
        'rating_max' => 5.0,
        'learned_min' => 10,
        'learned_max' => 20,
        'q' => 'basic',
        'page' => 1,
        'perPage' => 15,
    ]);

    $result = $repository->filter($filters);

    expect($result->total())->toBe(1);
    expect($result->items()[0]->name)->toBe('Basic English');
});

it('filters courses with min only conditions', function (): void {
    $category = Category::factory()->create();

    Course::factory()->create([
        'category_id' => $category->id,
        'name' => 'Math 101',
        'price' => 100,
        'status' => true,
        'rating' => 2.5,
        'learned' => 5,
    ]);

    Course::factory()->create([
        'category_id' => $category->id,
        'name' => 'Math 201',
        'price' => 300,
        'status' => true,
        'rating' => 4.2,
        'learned' => 25,
    ]);

    $repository = new CourseRepository(new Course);

    $filters = CourseFilter::fromArray([
        'price_min' => 200,
        'rating_min' => 4.0,
        'learned_min' => 10,
    ]);

    $result = $repository->filter($filters);

    expect($result->total())->toBe(1);
    expect($result->items()[0]->name)->toBe('Math 201');
});

it('filters courses with max only conditions', function (): void {
    $category = Category::factory()->create();

    Course::factory()->create([
        'category_id' => $category->id,
        'name' => 'Science 101',
        'price' => 100,
        'status' => true,
        'rating' => 2.0,
        'learned' => 5,
    ]);

    Course::factory()->create([
        'category_id' => $category->id,
        'name' => 'Science 201',
        'price' => 300,
        'status' => true,
        'rating' => 4.5,
        'learned' => 25,
    ]);

    $repository = new CourseRepository(new Course);

    $filters = CourseFilter::fromArray([
        'price_max' => 200,
        'rating_max' => 3.0,
        'learned_max' => 10,
    ]);

    $result = $repository->filter($filters);

    expect($result->total())->toBe(1);
    expect($result->items()[0]->name)->toBe('Science 101');
});

it('matches keyword in the middle of course name', function (): void {
    $category = Category::factory()->create();

    Course::factory()->create([
        'category_id' => $category->id,
        'name' => 'Basic English',
        'price' => 100,
        'status' => true,
        'rating' => 4.0,
        'learned' => 10,
    ]);

    $repository = new CourseRepository(new Course);

    $filters = CourseFilter::fromArray([
        'q' => 'asic',
    ]);

    $result = $repository->filter($filters);

    expect($result->total())->toBe(1);
    expect($result->items()[0]->name)->toBe('Basic English');
});

it('builds filter props from existing courses', function (): void {
    $categoryA = Category::factory()->create([
        'name' => 'Grammar Basics',
        'slug' => 'grammar-basics',
    ]);
    $categoryB = Category::factory()->create([
        'name' => 'Speaking Fluency',
        'slug' => 'speaking-fluency',
    ]);
    $unusedCategory = Category::factory()->create();

    Course::factory()->create([
        'category_id' => $categoryA->id,
        'name' => 'Course A',
        'price' => 100,
        'status' => true,
        'rating' => 2.5,
        'learned' => 5,
    ]);
    Course::factory()->create([
        'category_id' => $categoryB->id,
        'name' => 'Course B',
        'price' => 400,
        'status' => false,
        'rating' => 4.5,
        'learned' => 20,
    ]);
    Course::factory()->create([
        'category_id' => $categoryA->id,
        'name' => 'Course C',
        'price' => 250,
        'status' => true,
        'rating' => 3.8,
        'learned' => 12,
    ]);

    $repository = new CourseRepository(new Course);

    $props = $repository->getFilterProps();

    expect($props['price'])->toBe(['min' => 100, 'max' => 400]);
    expect($props['rating']['min'])->toBe(2.5);
    expect($props['rating']['max'])->toBe(4.5);
    expect($props['learned'])->toBe(['min' => 5, 'max' => 20]);
    expect($props['statuses'])->toBe([
        ['value' => true, 'label' => 'Active', 'count' => 2],
        ['value' => false, 'label' => 'Inactive', 'count' => 1],
    ]);

    expect($props['categories'])->toContain([
        'id' => $categoryA->id,
        'name' => 'Grammar Basics',
        'slug' => 'grammar-basics',
        'course_count' => 2,
    ]);
    expect($props['categories'])->toContain([
        'id' => $categoryB->id,
        'name' => 'Speaking Fluency',
        'slug' => 'speaking-fluency',
        'course_count' => 1,
    ]);
    expect(collect($props['categories'])->pluck('id')->all())->not()->toContain($unusedCategory->id);
});
