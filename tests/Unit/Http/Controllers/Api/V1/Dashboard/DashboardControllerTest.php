<?php

use App\DTO\Dashboard\DashboardOverviewResponse;
use App\DTO\Dashboard\DashboardStatsResponse;
use App\Http\Controllers\Api\V1\Dashboard\DashboardController;
use App\Models\User;
use App\Services\Dashboard\IDashboardService;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class);

it('returns dashboard overview for current user', function (): void {
    $user = new User;
    $user->id = 77;
    $request = Request::create('/dashboard', 'GET');
    $request->setUserResolver(fn () => $user);

    $overview = new DashboardOverviewResponse(
        stats: new DashboardStatsResponse(1, 2.5, 0, 3),
        enrolledCourses: [],
        recentActivity: [],
        certificates: [],
        weeklyPlan: [],
    );

    $service = Mockery::mock(IDashboardService::class);
    $service->shouldReceive('overview')->once()->with(77)->andReturn($overview);

    $response = (new DashboardController($service))->overview($request);

    assertJsonResponsePayload($response, 200, [
        'status' => true,
        'status_code' => 200,
        'data' => $overview->toArray(),
    ]);
});
