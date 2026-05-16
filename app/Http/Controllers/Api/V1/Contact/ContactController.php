<?php

namespace App\Http\Controllers\Api\V1\Contact;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Contact\ContactStoreRequest;
use App\Models\Contact;
use App\Services\Security\Recaptcha\IRecaptchaVerifier;
use App\Services\Security\Recaptcha\RecaptchaVerificationException;
use Illuminate\Http\JsonResponse;
use Throwable;

class ContactController extends ApiController
{
    public function __construct(
        private readonly IRecaptchaVerifier $recaptchaVerifier,
    ) {}

    public function store(ContactStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $this->recaptchaVerifier->verifyOrFail(
                token: $data['recaptcha_token'],
                expectedAction: (string) config('recaptcha.contact_action'),
                ipAddress: $request->ip(),
            );
        } catch (RecaptchaVerificationException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (Throwable) {
            return $this->error('Unable to submit contact request.', 422);
        }

        Contact::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'message' => $data['message'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->created(data: null, message: 'Contact submitted successfully.');
    }
}

