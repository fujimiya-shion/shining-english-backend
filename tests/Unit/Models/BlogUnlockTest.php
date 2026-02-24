<?php

use App\Models\BlogUnlock;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

it('defines fillable attributes', function (): void {
    $model = new BlogUnlock;

    expect($model->getFillable())->toEqual([
        'blog_id',
        'user_id',
    ]);
});

it('defines blog relation', function (): void {
    $method = new ReflectionMethod(BlogUnlock::class, 'blog');

    expect($method->getReturnType()?->getName())->toBe(BelongsTo::class);
    expect((new BlogUnlock)->blog())->toBeInstanceOf(BelongsTo::class);
});

it('defines user relation', function (): void {
    $method = new ReflectionMethod(BlogUnlock::class, 'user');

    expect($method->getReturnType()?->getName())->toBe(BelongsTo::class);
    expect((new BlogUnlock)->user())->toBeInstanceOf(BelongsTo::class);
});
