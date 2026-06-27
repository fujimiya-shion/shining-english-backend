<?php

use App\Http\Requests\Api\V1\Course\CourseReviewStoreRequest;

it('authorizes and defines course review validation rules and messages', function (): void {
    $request = new CourseReviewStoreRequest;

    expect($request->authorize())->toBeTrue();
    expect($request->rules())->toBe([
        'rating' => ['required', 'integer', 'min:1', 'max:5'],
        'content' => ['required', 'string'],
    ]);
    expect($request->messages())->toMatchArray([
        'rating.required' => 'Rating is required.',
        'rating.integer' => 'Rating must be an integer.',
        'rating.min' => 'Rating must be at least 1.',
        'rating.max' => 'Rating must be at most 5.',
        'content.required' => 'Content is required.',
        'content.string' => 'Content must be a string.',
    ]);
});
