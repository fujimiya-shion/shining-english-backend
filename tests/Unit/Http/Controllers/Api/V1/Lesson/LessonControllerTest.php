<?php

use App\Http\Controllers\Api\V1\Lesson\LessonController;
use App\Services\Lesson\ILessonService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use JsonSerializable;
use Tests\TestCase;

uses(TestCase::class);

afterEach(function (): void {
    \Mockery::close();
});

function makeLessonRequestWithId(int $id): Request
{
    $request = Request::create('/', 'GET');

    $request->setRouteResolver(function () use ($id) {
        return new class($id)
        {
            public function __construct(private int $id) {}

            public function parameter(string $key, $default = null)
            {
                return $key === 'id' ? $this->id : $default;
            }
        };
    });

    return $request;
}

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

it('returns notfound when lesson quiz has no lesson record', function (): void {
    $service = \Mockery::mock(ILessonService::class);
    $service->shouldReceive('getById')->once()->with(10)->andReturn(null);
    app()->instance(ILessonService::class, $service);

    $controller = app()->make(LessonController::class);
    $response = $controller->quiz(makeLessonRequestWithId(10));

    assertJsonResponsePayload($response, 404, [
        'message' => 'Not found',
        'status' => false,
        'status_code' => 404,
    ]);
});

it('returns quiz data for lesson quiz endpoint', function (): void {
    $quiz = new class implements JsonSerializable
    {
        public int $id = 1;
        public array $loaded = [];

        public function load(array $relations): static
        {
            $this->loaded = $relations;

            return $this;
        }

        public function jsonSerialize(): array
        {
            return ['id' => $this->id];
        }
    };

    $lesson = new class($quiz)
    {
        public function __construct(public object $quiz) {}
    };

    $service = \Mockery::mock(ILessonService::class);
    $service->shouldReceive('getById')->once()->with(10)->andReturn($lesson);
    app()->instance(ILessonService::class, $service);

    $controller = app()->make(LessonController::class);
    $response = $controller->quiz(makeLessonRequestWithId(10));

    expect($quiz->loaded)->toBe(['questions.answers']);

    assertJsonResponsePayload($response, 200, [
        'message' => 'Get Quiz Successfully',
        'status' => true,
        'status_code' => 200,
        'data' => ['id' => 1],
    ]);
});
