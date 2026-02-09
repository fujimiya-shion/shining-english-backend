<?php

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

it('defines fillable attributes', function (): void {
    $model = new Lesson;

    expect($model->getFillable())->toEqual([
        'name',
        'slug',
        'course_id',
        'video_url',
        'star_reward_video',
        'star_reward_quiz',
        'has_quiz',
    ]);
});

it('defines course relation', function (): void {
    $method = new ReflectionMethod(Lesson::class, 'course');

    expect($method->getReturnType()?->getName())->toBe(BelongsTo::class);
});

it('defines quiz relation', function (): void {
    $method = new ReflectionMethod(Lesson::class, 'quiz');

    expect($method->getReturnType()?->getName())->toBe(HasOne::class);
});
