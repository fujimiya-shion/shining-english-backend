<?php

use App\Http\Requests\Api\V1\Lesson\LessonCommentStoreRequest;

it('authorizes and defines lesson comment validation rules and messages', function (): void {
    $request = new LessonCommentStoreRequest;

    expect($request->authorize())->toBeTrue();
    expect($request->rules())->toBe([
        'content' => ['required', 'string'],
    ]);
    expect($request->messages())->toBe([
        'content.required' => 'Content is required.',
        'content.string' => 'Content must be a string.',
    ]);
});
