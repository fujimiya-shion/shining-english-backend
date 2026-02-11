<?php

use App\Filament\Resources\Courses\Schemas\CourseForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

test('course form defines expected components', function (): void {
    $schema = CourseForm::configure(makeSchema());

    $components = schemaComponentMap($schema);

    expect(array_keys($components))->toEqual([
        'name',
        'slug',
        'price',
        'status',
        'thumbnail',
        'category_id',
    ]);

    expect($components['name'])->toBeInstanceOf(TextInput::class);
    expect($components['slug'])->toBeInstanceOf(TextInput::class);
    expect($components['price'])->toBeInstanceOf(TextInput::class);
    expect($components['status'])->toBeInstanceOf(Toggle::class);
    expect($components['thumbnail'])->toBeInstanceOf(TextInput::class);
    expect($components['category_id'])->toBeInstanceOf(Select::class);
});

test('course form marks required fields', function (): void {
    $schema = CourseForm::configure(makeSchema());

    $components = schemaComponentMap($schema);

    expect($components['name']->isRequired())->toBeTrue();
    expect($components['slug']->isRequired())->toBeTrue();
    expect($components['price']->isRequired())->toBeTrue();
    expect($components['status']->isRequired())->toBeTrue();
    expect($components['category_id']->isRequired())->toBeTrue();
});

test('course form configures numeric price input', function (): void {
    $schema = CourseForm::configure(makeSchema());

    $components = schemaComponentMap($schema);

    expect($components['price']->isNumeric())->toBeTrue();
});
