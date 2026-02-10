<?php

use Illuminate\Http\JsonResponse;
trait Jsonable {
    public function success(
        string $message = 'OK',
        mixed $data = null,
        int $statusCode = 200,
        mixed $meta = null
    ): JsonResponse {
        return response()->json([
            'message' => $message,
            'status' => true,
            'status_code' => $statusCode,
            'data' => $data,
            'meta' => $meta,
        ], $statusCode);
    }

    public function error(
        string $message = 'Error',
        int $statusCode = 500,
        mixed $errors = null,
    ): JsonResponse {
        return response()->json([
            'message' => $message,
            'status' => false,
            'status_code' => $statusCode,
            'errors' => $errors,
        ], $statusCode);
    }
}