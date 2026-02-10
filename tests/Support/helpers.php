<?php

use Illuminate\Http\JsonResponse;

if (!function_exists('assertRepositoryContract')) {
    /**
     * Assert a repository implements its interface and stores the given model instance.
     */
    function assertRepositoryContract(object $repository, string $interface, object $model): void
    {
        expect($repository)->toBeInstanceOf($interface);

        $actualModel = (fn () => $this->model)->call($repository);
        expect($actualModel)->toBe($model);
    }
}

if(!function_exists('assertServiceContract')) {
    function assertServiceContract(object $service, string $interface, object $repository): void 
    {
        expect($service)->toBeInstanceOf($interface);

        $actialRepository = (fn () => $this->repository)->call($service);
        expect($actialRepository)->toBe($repository);
    }
}

if (!function_exists('assertJsonResponsePayload')) {
    /**
     * Assert JsonResponse status and payload keys/values.
     *
     * @param  array<string, mixed>  $expectedPayload
     */
    function assertJsonResponsePayload(JsonResponse $response, int $statusCode, array $expectedPayload): void
    {
        expect($response)->toBeInstanceOf(JsonResponse::class);
        expect($response->getStatusCode())->toBe($statusCode);
        expect($response->getData(true))->toMatchArray($expectedPayload);
    }
}
