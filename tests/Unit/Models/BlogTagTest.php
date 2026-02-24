<?php

use App\Models\BlogTag;
use Illuminate\Database\Eloquent\Relations\HasMany;

it('defines fillable attributes', function (): void {
    $model = new BlogTag;

    expect($model->getFillable())->toEqual([
        'name',
        'slug',
    ]);
});

it('defines blogs relation', function (): void {
    $method = new ReflectionMethod(BlogTag::class, 'blogs');

    expect($method->getReturnType()?->getName())->toBe(HasMany::class);
    expect((new BlogTag)->blogs())->toBeInstanceOf(HasMany::class);
});

it('uses soft deletes', function (): void {
    $model = new BlogTag;

    expect(method_exists($model, 'trashed'))->toBeTrue();
});
