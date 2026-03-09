<?php

use App\Http\Controllers\Api\V1\Course\CourseController;
use App\Http\Requests\Api\V1\Course\CourseFilterRequest;
use App\Models\Level;
use App\Services\Course\ICourseService;
use App\ValueObjects\CourseFilter;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Tests\TestCase;

uses(TestCase::class);
uses(DatabaseMigrations::class);

afterEach(function (): void {
    \Mockery::close();
});

it('can be instantiated', function (): void {
    $controller = app()->make(CourseController::class);

    expect($controller)->toBeInstanceOf(CourseController::class);
});

it('returns success response from index', function (): void {
    $items = new Collection;
    $paginator = new LengthAwarePaginator($items, 0, 15, 1);

    $service = \Mockery::mock(ICourseService::class);
    $service->shouldReceive('paginateAll')->once()->andReturn($paginator);
    app()->instance(ICourseService::class, $service);

    $controller = app()->make(CourseController::class);
    $response = $controller->index(new Request);

    assertJsonResponsePayload($response, 200, [
        'message' => 'OK',
        'status' => true,
        'status_code' => 200,
        'meta' => [
            'page' => 1,
            'per_page' => 15,
            'total' => 0,
            'page_count' => 0,
        ],
    ]);
});

it('inherits success and error json helpers', function (): void {
    $controller = app()->make(CourseController::class);

    $success = $controller->success('OK', ['id' => 1], 200);
    $error = $controller->error('Bad Request', 400, ['field' => ['invalid']]);

    assertJsonResponsePayload($success, 200, [
        'message' => 'OK',
        'status' => true,
        'status_code' => 200,
    ]);

    assertJsonResponsePayload($error, 400, [
        'message' => 'Bad Request',
        'status' => false,
        'status_code' => 400,
    ]);
});

it('filters courses with supported criteria', function (): void {
    $items = new Collection;
    $paginator = new LengthAwarePaginator($items, 0, 15, 1);
    $level = Level::factory()->create();

    $service = \Mockery::mock(ICourseService::class);
    $service->shouldReceive('filter')
        ->once()
        ->with(
            \Mockery::on(function (CourseFilter $filters) use ($level): bool {
                return $filters->categoryId === 2
                    && $filters->status === false
                    && $filters->levelId === $level->id
                    && $filters->priceMin === 100
                    && $filters->priceMax === 300
                    && $filters->ratingMin === 3.5
                    && $filters->ratingMax === 4.5
                    && $filters->learnedMin === 10
                    && $filters->learnedMax === 20
                    && $filters->keyword === 'basic';
            })
        )
        ->andReturn($paginator);
    app()->instance(ICourseService::class, $service);

    $controller = app()->make(CourseController::class);
    $request = CourseFilterRequest::create('/api/v1/courses/filter', 'GET', [
        'category_id' => 2,
        'status' => false,
        'level_id' => $level->id,
        'price_min' => 100,
        'price_max' => 300,
        'rating_min' => 3.5,
        'rating_max' => 4.5,
        'learned_min' => 10,
        'learned_max' => 20,
        'q' => 'basic',
    ]);
    $request->setContainer(app())->setRedirector(app('redirect'));
    $request->validateResolved();

    $response = $controller->filter($request);

    assertJsonResponsePayload($response, 200, [
        'message' => 'OK',
        'status' => true,
        'status_code' => 200,
        'meta' => [
            'page' => 1,
            'per_page' => 15,
            'total' => 0,
            'page_count' => 0,
        ],
    ]);
});

it('returns filter props from service', function (): void {
    $payload = [
        'categories' => [
            [
                'id' => 1,
                'name' => 'Grammar Basics',
                'slug' => 'grammar-basics',
                'course_count' => 10,
            ],
        ],
        'price' => ['min' => 100, 'max' => 500],
        'rating' => ['min' => 1.0, 'max' => 5.0],
        'learned' => ['min' => 0, 'max' => 100],
        'statuses' => [
            ['value' => true, 'label' => 'Active', 'count' => 10],
            ['value' => false, 'label' => 'Inactive', 'count' => 0],
        ],
        'levels' => [
            ['value' => 1, 'label' => 'Beginner', 'count' => 5],
            ['value' => 2, 'label' => 'Intermediate', 'count' => 3],
            ['value' => 3, 'label' => 'Advanced', 'count' => 2],
        ],
    ];

    $service = \Mockery::mock(ICourseService::class);
    $service->shouldReceive('getFilterProps')->once()->andReturn($payload);
    app()->instance(ICourseService::class, $service);

    $controller = app()->make(CourseController::class);
    $response = $controller->getFilterProps();

    assertJsonResponsePayload($response, 200, [
        'message' => 'OK',
        'status' => true,
        'status_code' => 200,
        'data' => $payload,
    ]);
});
