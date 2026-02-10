<?php

use App\Services\IService;
use App\Traits\ApiBehaviour;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Tests\TestCase;

uses(TestCase::class);

afterEach(function (): void {
    \Mockery::close();
});

function makeRequestWithId(int $id, string $method = 'GET', array $data = []): Request
{
    $request = Request::create('/', $method, $data);

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

function makeApiBehaviourController(IService $service)
{
    return new class($service)
    {
        use ApiBehaviour;

        public function __construct(private IService $service) {}

        protected function service(): IService
        {
            return $this->service;
        }
    };
}

it('returns success with meta on index', function (): void {
    $items = new Collection([['id' => 1]]);
    $paginator = new LengthAwarePaginator($items, 1, 15, 1);

    $service = \Mockery::mock(IService::class);
    $service->shouldReceive('paginateAll')->once()->andReturn($paginator);

    $controller = makeApiBehaviourController($service);
    $response = $controller->index(new Request);

    assertJsonResponsePayload($response, 200, [
        'message' => 'OK',
        'status' => true,
        'status_code' => 200,
        'meta' => [
            'page' => 1,
            'per_page' => 15,
            'total' => 1,
            'page_count' => 1,
        ],
    ]);
});

it('returns notfound when show has no record', function (): void {
    $service = \Mockery::mock(IService::class);
    $service->shouldReceive('getById')->once()->with(10)->andReturn(null);

    $controller = makeApiBehaviourController($service);
    $response = $controller->show(makeRequestWithId(10));

    assertJsonResponsePayload($response, 404, [
        'message' => 'Not found',
        'status' => false,
        'status_code' => 404,
    ]);
});

it('returns success when show finds record', function (): void {
    $record = new FakeModel(['id' => 10]);
    $service = \Mockery::mock(IService::class);
    $service->shouldReceive('getById')->once()->with(10)->andReturn($record);

    $controller = makeApiBehaviourController($service);
    $response = $controller->show(makeRequestWithId(10));

    assertJsonResponsePayload($response, 200, [
        'message' => 'OK',
        'status' => true,
        'status_code' => 200,
        'data' => ['id' => $record->id],
    ]);
});

it('returns created on store success', function (): void {
    $record = new FakeModel(['id' => 1]);
    $service = \Mockery::mock(IService::class);
    $service->shouldReceive('create')->once()->with(['name' => 'Test'])->andReturn($record);

    $controller = makeApiBehaviourController($service);
    $response = $controller->store(Request::create('/', 'POST', ['name' => 'Test']));

    assertJsonResponsePayload($response, 201, [
        'message' => 'Created',
        'status' => true,
        'status_code' => 201,
        'data' => ['id' => $record->id],
    ]);
});

it('returns error on store exception', function (): void {
    $service = \Mockery::mock(IService::class);
    $service->shouldReceive('create')->once()->andThrow(new Exception('fail'));

    $controller = makeApiBehaviourController($service);
    $response = $controller->store(Request::create('/', 'POST', ['name' => 'Test']));

    assertJsonResponsePayload($response, 500, [
        'message' => 'Error',
        'status' => false,
        'status_code' => 500,
    ]);
});

it('returns success on update', function (): void {
    $record = new FakeModel(['id' => 2]);
    $service = \Mockery::mock(IService::class);
    $service->shouldReceive('update')->once()->with(2, ['name' => 'New'])->andReturn($record);

    $controller = makeApiBehaviourController($service);
    $response = $controller->update(makeRequestWithId(2, 'PUT', ['name' => 'New']));

    assertJsonResponsePayload($response, 200, [
        'message' => 'Updated',
        'status' => true,
        'status_code' => 200,
        'data' => ['id' => $record->id],
    ]);
});

it('returns error on update exception', function (): void {
    $service = \Mockery::mock(IService::class);
    $service->shouldReceive('update')->once()->andThrow(new Exception('fail'));

    $controller = makeApiBehaviourController($service);
    $response = $controller->update(makeRequestWithId(2, 'PUT', ['name' => 'New']));

    assertJsonResponsePayload($response, 500, [
        'message' => 'Error',
        'status' => false,
        'status_code' => 500,
    ]);
});

it('returns deleted when delete succeeds', function (): void {
    $service = \Mockery::mock(IService::class);
    $service->shouldReceive('delete')->once()->with(5)->andReturn(true);

    $controller = makeApiBehaviourController($service);
    $response = $controller->delete(makeRequestWithId(5, 'DELETE'));

    assertJsonResponsePayload($response, 200, [
        'message' => 'Deleted',
        'status' => true,
        'status_code' => 200,
    ]);
});

it('returns notfound when delete returns false', function (): void {
    $service = \Mockery::mock(IService::class);
    $service->shouldReceive('delete')->once()->with(6)->andReturn(false);

    $controller = makeApiBehaviourController($service);
    $response = $controller->delete(makeRequestWithId(6, 'DELETE'));

    assertJsonResponsePayload($response, 404, [
        'message' => 'Not found',
        'status' => false,
        'status_code' => 404,
    ]);
});

it('returns error when delete throws', function (): void {
    $service = \Mockery::mock(IService::class);
    $service->shouldReceive('delete')->once()->andThrow(new Exception('fail'));

    $controller = makeApiBehaviourController($service);
    $response = $controller->delete(makeRequestWithId(7, 'DELETE'));

    assertJsonResponsePayload($response, 500, [
        'message' => 'Error',
        'status' => false,
        'status_code' => 500,
    ]);
});

class FakeModel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'fake_models';

    protected $guarded = [];

    public $timestamps = false;
}
