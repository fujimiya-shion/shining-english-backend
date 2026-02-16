<?php

use App\Http\Controllers\Api\V1\Course\CourseController;
use App\Http\Requests\Api\V1\Course\CourseFilterRequest;
use App\Services\Course\ICourseService;
use App\ValueObjects\CourseFilter;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Tests\TestCase;

uses(TestCase::class);

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

    $service = \Mockery::mock(ICourseService::class);
    $service->shouldReceive('filter')
        ->once()
        ->with(
            \Mockery::on(function (CourseFilter $filters): bool {
                return $filters->categoryId === 2
                    && $filters->status === false
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
