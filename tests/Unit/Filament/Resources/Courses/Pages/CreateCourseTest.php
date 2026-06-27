<?php

use App\Filament\Resources\Courses\Pages\CreateCourse;
use Illuminate\Validation\ValidationException;

test('create course accepts uploaded thumbnail', function (): void {
    $page = new CreateCourse;

    $data = invokeProtectedMethod($page, 'mutateFormDataBeforeCreate', [[
        'thumbnail_source' => 'upload',
        'thumbnail_file' => 'courses/thumb.jpg',
        'thumbnail_url' => '',
        'name' => 'Course name',
    ]]);

    expect($data['thumbnail'])->toBe('courses/thumb.jpg');
    expect($data)->not->toHaveKey('thumbnail_source');
    expect($data)->not->toHaveKey('thumbnail_file');
    expect($data)->not->toHaveKey('thumbnail_url');
});

test('create course accepts external thumbnail url', function (): void {
    $page = new CreateCourse;

    $data = invokeProtectedMethod($page, 'mutateFormDataBeforeCreate', [[
        'thumbnail_source' => 'url',
        'thumbnail_file' => '',
        'thumbnail_url' => 'https://example.com/course.jpg',
    ]]);

    expect($data['thumbnail'])->toBe('https://example.com/course.jpg');
});

test('create course validates thumbnail input', function (array $payload, string $field): void {
    $page = new CreateCourse;

    try {
        invokeProtectedMethod($page, 'mutateFormDataBeforeCreate', [$payload]);
        $this->fail('Expected validation exception.');
    } catch (ValidationException $exception) {
        expect($exception->errors())->toHaveKey($field);
    }
})->with([
    'missing url' => [[
        'thumbnail_source' => 'url',
        'thumbnail_url' => '',
    ], 'thumbnail_url'],
    'invalid url' => [[
        'thumbnail_source' => 'url',
        'thumbnail_url' => 'not-a-url',
    ], 'thumbnail_url'],
    'missing upload' => [[
        'thumbnail_source' => 'upload',
        'thumbnail_file' => '',
    ], 'thumbnail_file'],
]);
