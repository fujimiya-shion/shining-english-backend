<?php

use App\Filament\Resources\Lessons\Schemas\LessonForm;
use App\Models\Lesson;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

test('lesson form defines expected components', function (): void {
    $schema = LessonForm::configure(makeSchema()->model(Lesson::class));

    $components = schemaComponentMap($schema);

    expect(array_keys($components))->toEqual([
        'name',
        'slug',
        'course_id',
        'video_url',
        'star_reward_video',
        'star_reward_quiz',
        'has_quiz',
        'pass_percent',
    ]);

    expect($components['name'])->toBeInstanceOf(TextInput::class);
    expect($components['slug'])->toBeInstanceOf(TextInput::class);
    expect($components['course_id'])->toBeInstanceOf(Select::class);
    expect($components['video_url'])->toBeInstanceOf(FileUpload::class);
    expect($components['star_reward_video'])->toBeInstanceOf(TextInput::class);
    expect($components['star_reward_quiz'])->toBeInstanceOf(TextInput::class);
    expect($components['has_quiz'])->toBeInstanceOf(Toggle::class);
});

test('lesson form marks required fields', function (): void {
    $schema = LessonForm::configure(makeSchema()->model(Lesson::class));

    $components = schemaComponentMap($schema);

    expect($components['name']->isRequired())->toBeTrue();
    expect($components['course_id']->isRequired())->toBeTrue();
    expect($components['video_url']->isRequired())->toBeTrue();
    expect($components['pass_percent']->isRequired())->toBeTrue();
});

test('lesson form configures numeric star inputs', function (): void {
    $schema = LessonForm::configure(makeSchema()->model(Lesson::class));

    $components = schemaComponentMap($schema);

    expect($components['star_reward_video']->isNumeric())->toBeTrue();
    expect($components['star_reward_quiz']->isNumeric())->toBeTrue();
});
