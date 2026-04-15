<?php

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

test('lesson model defaults star rewards to zero', function (): void {
    $lesson = new Lesson();

    expect($lesson->star_reward_video)->toBe(0);
    expect($lesson->star_reward_quiz)->toBe(0);
    expect($lesson->has_quiz)->toBeFalse();
});

it('defines fillable attributes', function (): void {
    $model = new Lesson;

    expect($model->getFillable())->toEqual([
        'name',
        'slug',
        'course_id',
        'group_name',
        'video_url',
        'description',
        'duration_minutes',
        'star_reward_video',
        'star_reward_quiz',
        'has_quiz',
    ]);
});

it('defines course relation', function (): void {
    $method = new ReflectionMethod(Lesson::class, 'course');

    expect($method->getReturnType()?->getName())->toBe(BelongsTo::class);
    expect((new Lesson)->course())->toBeInstanceOf(BelongsTo::class);
});

it('defines quiz relation', function (): void {
    $method = new ReflectionMethod(Lesson::class, 'quiz');

    expect($method->getReturnType()?->getName())->toBe(HasOne::class);
    expect((new Lesson)->quiz())->toBeInstanceOf(HasOne::class);
});

it('defines comments relation', function (): void {
    $method = new ReflectionMethod(Lesson::class, 'comments');

    expect($method->getReturnType()?->getName())->toBe(HasMany::class);
    expect((new Lesson)->comments())->toBeInstanceOf(HasMany::class);
});

it('defines notes relation', function (): void {
    $method = new ReflectionMethod(Lesson::class, 'notes');

    expect($method->getReturnType()?->getName())->toBe(HasMany::class);
    expect((new Lesson)->notes())->toBeInstanceOf(HasMany::class);
});

it('defines casts for lesson attributes', function (): void {
    $model = new Lesson;

    expect($model->getCasts())->toMatchArray([
        'has_quiz' => 'boolean',
        'duration_minutes' => 'integer',
    ]);
});

it('uses soft deletes', function (): void {
    $model = new Lesson;

    expect(method_exists($model, 'trashed'))->toBeTrue();
});
