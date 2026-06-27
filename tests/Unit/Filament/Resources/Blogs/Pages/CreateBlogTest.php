<?php

use App\Filament\Resources\Blogs\Pages\CreateBlog;
use Illuminate\Validation\ValidationException;

test('create blog accepts uploaded thumbnail', function (): void {
    $page = new CreateBlog;

    $data = invokeProtectedMethod($page, 'mutateFormDataBeforeCreate', [[
        'thumbnail_source' => 'upload',
        'thumbnail_file' => 'blogs/thumb.jpg',
        'thumbnail_url' => '',
        'title' => 'Blog title',
    ]]);

    expect($data['thumbnail'])->toBe('blogs/thumb.jpg');
    expect($data)->not->toHaveKey('thumbnail_source');
    expect($data)->not->toHaveKey('thumbnail_file');
    expect($data)->not->toHaveKey('thumbnail_url');
});

test('create blog accepts external thumbnail url', function (): void {
    $page = new CreateBlog;

    $data = invokeProtectedMethod($page, 'mutateFormDataBeforeCreate', [[
        'thumbnail_source' => 'url',
        'thumbnail_file' => '',
        'thumbnail_url' => 'https://example.com/thumb.jpg',
    ]]);

    expect($data['thumbnail'])->toBe('https://example.com/thumb.jpg');
});

test('create blog validates thumbnail input', function (array $payload, string $field): void {
    $page = new CreateBlog;

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
