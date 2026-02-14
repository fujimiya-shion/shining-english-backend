<?php

use App\Filament\Resources\Quizzes\Schemas\QuizForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

test('quiz form defines expected components', function (): void {
    $schema = QuizForm::configure(makeSchema());

    $components = schemaComponentMap($schema);

    expect(array_keys($components))->toEqual([
        'lesson_id',
        'pass_percent',
    ]);

    expect($components['lesson_id'])->toBeInstanceOf(Select::class);
    expect($components['pass_percent'])->toBeInstanceOf(TextInput::class);
});

test('quiz form marks required fields', function (): void {
    $schema = QuizForm::configure(makeSchema());

    $components = schemaComponentMap($schema);

    expect($components['lesson_id']->isRequired())->toBeTrue();
    expect($components['pass_percent']->isRequired())->toBeTrue();
});

test('quiz form configures numeric pass percent', function (): void {
    $schema = QuizForm::configure(makeSchema());

    $components = schemaComponentMap($schema);

    expect($components['pass_percent']->isNumeric())->toBeTrue();
});

test('quiz form enforces unique lesson selection', function (): void {
    $schema = QuizForm::configure(makeSchema());

    $components = schemaComponentMap($schema);

    $rules = getProtectedPropertyValue($components['lesson_id'], 'rules');

    $firstRule = $rules[0] ?? null;

    expect($firstRule)->not->toBeNull();
    expect($firstRule[0])->toBeInstanceOf(Closure::class);
});
