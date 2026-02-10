<?php

use App\Traits\Jsonable;
use Tests\TestCase;

uses(TestCase::class);

it('returns success json response with default values', function (): void {
    $target = new class
    {
        use Jsonable;
    };

    $response = $target->success();

    assertJsonResponsePayload($response, 200, [
        'message' => 'OK',
        'status' => true,
        'status_code' => 200,
        'data' => null,
        'meta' => null,
    ]);
});

it('returns success json response with custom values', function (): void {
    $target = new class
    {
        use Jsonable;
    };

    $response = $target->success(
        'Created',
        ['id' => 1, 'name' => 'Course'],
        201,
        ['page' => 1]
    );

    assertJsonResponsePayload($response, 201, [
        'message' => 'Created',
        'status' => true,
        'status_code' => 201,
        'data' => ['id' => 1, 'name' => 'Course'],
        'meta' => ['page' => 1],
    ]);
});

it('returns error json response with default values', function (): void {
    $target = new class
    {
        use Jsonable;
    };

    $response = $target->error();

    assertJsonResponsePayload($response, 500, [
        'message' => 'Error',
        'status' => false,
        'status_code' => 500,
        'errors' => null,
    ]);
});

it('returns error json response with custom values', function (): void {
    $target = new class
    {
        use Jsonable;
    };

    $response = $target->error(
        'Validation failed',
        422,
        ['email' => ['The email field is required.']]
    );

    assertJsonResponsePayload($response, 422, [
        'message' => 'Validation failed',
        'status' => false,
        'status_code' => 422,
        'errors' => ['email' => ['The email field is required.']],
    ]);
});

it('returns notfound response', function (): void {
    $target = new class
    {
        use Jsonable;
    };

    $response = $target->notfound();

    assertJsonResponsePayload($response, 404, [
        'message' => 'Not found',
        'status' => false,
        'status_code' => 404,
    ]);
});

it('returns created response', function (): void {
    $target = new class
    {
        use Jsonable;
    };

    $response = $target->created(['id' => 1]);

    assertJsonResponsePayload($response, 201, [
        'message' => 'Created',
        'status' => true,
        'status_code' => 201,
        'data' => ['id' => 1],
    ]);
});

it('returns deleted response', function (): void {
    $target = new class
    {
        use Jsonable;
    };

    $response = $target->deleted();

    assertJsonResponsePayload($response, 204, [
        'message' => 'Deleted',
        'status' => true,
        'status_code' => 204,
    ]);
});
