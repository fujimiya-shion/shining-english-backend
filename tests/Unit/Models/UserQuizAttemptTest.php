<?php

use App\Models\UserQuizAttempt;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

it('defines fillable attributes', function (): void {
    $model = new UserQuizAttempt;

    expect($model->getFillable())->toEqual([
        'user_id',
        'quiz_id',
        'score_percent',
        'passed',
        'submitted_at',
    ]);
});

it('casts attributes correctly', function (): void {
    $model = new UserQuizAttempt;

    expect($model->getCasts())->toMatchArray([
        'score_percent' => 'float',
        'passed' => 'boolean',
        'submitted_at' => 'datetime',
    ]);
});

it('defines user relation', function (): void {
    $method = new ReflectionMethod(UserQuizAttempt::class, 'user');

    expect($method->getReturnType()?->getName())->toBe(BelongsTo::class);
});

it('defines quiz relation', function (): void {
    $method = new ReflectionMethod(UserQuizAttempt::class, 'quiz');

    expect($method->getReturnType()?->getName())->toBe(BelongsTo::class);
});
