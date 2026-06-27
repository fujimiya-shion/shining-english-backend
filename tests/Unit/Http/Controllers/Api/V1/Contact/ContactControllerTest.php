<?php

use App\Http\Controllers\Api\V1\Contact\ContactController;
use App\Http\Requests\Api\V1\Contact\ContactStoreRequest;
use App\Services\Contact\IContactService;
use App\Services\Security\Recaptcha\IRecaptchaVerifier;
use Tests\TestCase;

uses(TestCase::class);

it('returns generic contact error when recaptcha verifier throws unexpected exception', function (): void {
    $verifier = Mockery::mock(IRecaptchaVerifier::class);
    $verifier->shouldReceive('verifyOrFail')
        ->once()
        ->andThrow(new RuntimeException('unexpected'));

    $request = ContactStoreRequest::create('/contact', 'POST', [
        'name' => 'Learner',
        'email' => 'learner@example.com',
        'message' => 'Need support',
        'recaptcha_token' => 'token',
    ]);
    $request->setContainer(app())->setRedirector(app('redirect'));
    $request->validateResolved();

    $controller = new ContactController($verifier, Mockery::mock(IContactService::class));
    $response = $controller->store($request);

    assertJsonResponsePayload($response, 422, [
        'status' => false,
        'status_code' => 422,
        'message' => 'Unable to submit contact request.',
    ]);
});
