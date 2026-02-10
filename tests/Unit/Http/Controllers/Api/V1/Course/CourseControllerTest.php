<?php

use App\Http\Controllers\Api\V1\Course\CourseController;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class);

it('can be instantiated', function (): void {
    $controller = new CourseController;

    expect($controller)->toBeInstanceOf(CourseController::class);
});

it('returns null from index for current implementation', function (): void {
    $controller = new CourseController;

    expect($controller->index(new Request))->toBeNull();
});

it('inherits success and error json helpers', function (): void {
    $controller = new CourseController;

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
