<?php

use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

it('defines fillable attributes', function (): void {
    $model = new Enrollment;

    expect($model->getFillable())->toEqual([
        'user_id',
        'course_id',
        'order_id',
        'enrolled_at',
    ]);
});

it('casts attributes correctly', function (): void {
    $model = new Enrollment;

    expect($model->getCasts())->toMatchArray([
        'enrolled_at' => 'datetime',
    ]);
});

it('uses soft deletes', function (): void {
    $model = new Enrollment;

    expect(method_exists($model, 'trashed'))->toBeTrue();
});

it('defines user relation', function (): void {
    $method = new ReflectionMethod(Enrollment::class, 'user');

    expect($method->getReturnType()?->getName())->toBe(BelongsTo::class);
    expect((new Enrollment)->user())->toBeInstanceOf(BelongsTo::class);
});

it('defines course relation', function (): void {
    $method = new ReflectionMethod(Enrollment::class, 'course');

    expect($method->getReturnType()?->getName())->toBe(BelongsTo::class);
    expect((new Enrollment)->course())->toBeInstanceOf(BelongsTo::class);
});

it('defines order relation', function (): void {
    $method = new ReflectionMethod(Enrollment::class, 'order');

    expect($method->getReturnType()?->getName())->toBe(BelongsTo::class);
    expect((new Enrollment)->order())->toBeInstanceOf(BelongsTo::class);
});
