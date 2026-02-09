<?php

use App\Models\Course;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

it('defines fillable attributes', function (): void {
    $model = new Course;

    expect($model->getFillable())->toEqual([
        'name',
        'slug',
        'price',
        'status',
        'thumbnail',
        'category_id',
    ]);
});

it('defines category relation', function (): void {
    $method = new ReflectionMethod(Course::class, 'category');

    expect($method->getReturnType()?->getName())->toBe(BelongsTo::class);
});

it('defines lessons relation', function (): void {
    $method = new ReflectionMethod(Course::class, 'lessons');

    expect($method->getReturnType()?->getName())->toBe(HasMany::class);
});
