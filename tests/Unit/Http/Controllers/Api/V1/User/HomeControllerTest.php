<?php

use App\DTO\User\Page\Home\HomeResponse;
use App\Http\Controllers\Api\V1\User\HomeController;
use App\Services\User\IUserHomeService;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class);

it('returns home payload using user authorization header', function (): void {
    $service = Mockery::mock(IUserHomeService::class);
    $service->shouldReceive('getHomeData')
        ->once()
        ->with('Bearer user-token')
        ->andReturn(new HomeResponse([]));

    $request = Request::create('/home', 'GET', server: [
        'HTTP_USER_AUTHORIZATION' => 'Bearer user-token',
    ]);

    $response = (new HomeController($service))->index($request);

    assertJsonResponsePayload($response, 200, [
        'status' => true,
        'status_code' => 200,
        'data' => ['payloads' => []],
    ]);
});
