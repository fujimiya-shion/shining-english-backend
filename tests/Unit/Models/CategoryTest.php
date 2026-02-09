<?php

use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;

it('defines fillable attributes', function (): void {
    $model = new Category;

    expect($model->getFillable())->toEqual([
        'name',
        'slug',
    ]);
});

it('defines courses relation', function (): void {
    $method = new ReflectionMethod(Category::class, 'courses');

    expect($method->getReturnType()?->getName())->toBe(HasMany::class);
});
