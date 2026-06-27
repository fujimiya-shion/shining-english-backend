<?php

use App\Http\Controllers\Api\V1\Blog\BlogController;
use App\Models\Blog;
use App\Models\BlogTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

it('lists and shows active blogs', function (): void {
    config(['app.url' => 'https://app.test']);

    $tag = BlogTag::query()->create(['name' => 'Grammar', 'slug' => 'grammar']);
    Blog::query()->create([
        'title' => 'Learn grammar',
        'description' => 'Description',
        'short_description' => 'Short',
        'thumbnail' => 'blogs/thumb.jpg',
        'content' => '<p>Hello world</p>',
        'slug' => 'learn-grammar',
        'status' => true,
        'tag_id' => $tag->id,
    ]);

    $controller = new BlogController;

    $index = $controller->index(Request::create('/blogs', 'GET'));
    assertJsonResponsePayload($index, 200, [
        'status' => true,
        'status_code' => 200,
    ]);
    expect($index->getData(true)['data']['items'][0]['content'])->toBeNull();
    expect($index->getData(true)['data']['topics'][0]['slug'])->toBe('grammar');

    $show = $controller->showBySlug(Request::create('/blogs/learn-grammar', 'GET'), 'learn-grammar');
    assertJsonResponsePayload($show, 200, [
        'status' => true,
        'status_code' => 200,
    ]);
    expect($show->getData(true)['data']['blog']['content'])->toBe('<p>Hello world</p>');
    expect($controller->showBySlug(Request::create('/blogs/missing', 'GET'), 'missing')->getStatusCode())->toBe(404);
});
