<?php

use App\Http\Controllers\Api\V1\Lesson\LessonController;
use App\Services\Lesson\ILessonService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Tests\TestCase;

uses(TestCase::class);

afterEach(function (): void {
    \Mockery::close();
});

it('can be instantiated', function (): void {
    $controller = app()->make(LessonController::class);

    expect($controller)->toBeInstanceOf(LessonController::class);
});

it('returns success response from index', function (): void {
    $items = new Collection;
    $paginator = new LengthAwarePaginator($items, 0, 15, 1);

    $service = \Mockery::mock(ILessonService::class);
    $service->shouldReceive('paginateAll')->once()->andReturn($paginator);
    app()->instance(ILessonService::class, $service);

    $controller = app()->make(LessonController::class);
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
