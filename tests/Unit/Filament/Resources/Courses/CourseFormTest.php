<?php

use App\Filament\Resources\Courses\Schemas\CourseForm;
use App\Models\Level;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);

test('course form defines expected components', function (): void {
    $schema = CourseForm::configure(makeSchema());

    $components = schemaComponentMap($schema);

    expect(array_keys($components))->toEqual([
        'status',
        'name',
        'slug',
        'category_id',
        'levels',
        'price',
        'rating',
        'learned',
        'thumbnail',
        'description',
    ]);

    expect($components['status'])->toBeInstanceOf(Toggle::class);
    expect($components['name'])->toBeInstanceOf(TextInput::class);
    expect($components['slug'])->toBeInstanceOf(TextInput::class);
    expect($components['category_id'])->toBeInstanceOf(Select::class);
    expect($components['levels'])->toBeInstanceOf(Select::class);
    expect($components['price'])->toBeInstanceOf(TextInput::class);
    expect($components['rating'])->toBeInstanceOf(TextInput::class);
    expect($components['learned'])->toBeInstanceOf(TextInput::class);
    expect($components['thumbnail'])->toBeInstanceOf(FileUpload::class);
    expect($components['description'])->toBeInstanceOf(RichEditor::class);
});

test('course form marks required fields', function (): void {
    $schema = CourseForm::configure(makeSchema());

    $components = schemaComponentMap($schema);

    expect($components['name']->isRequired())->toBeTrue();
    expect($components['slug']->isRequired())->toBeFalse();
    expect($components['price']->isRequired())->toBeTrue();
    expect($components['status']->isRequired())->toBeTrue();
    expect($components['category_id']->isRequired())->toBeTrue();
});

test('course form configures numeric price input', function (): void {
    $schema = CourseForm::configure(makeSchema());

    $components = schemaComponentMap($schema);

    expect($components['price']->isNumeric())->toBeTrue();
});

test('course form create option creates unique level slug', function (): void {
    Level::factory()->create([
        'name' => 'Exam Prep',
        'slug' => 'exam-prep',
    ]);

    $schema = CourseForm::configure(makeSchema());
    $components = schemaComponentMap($schema);
    /** @var Select $levelSelect */
    $levelSelect = $components['levels'];
    $createOptionUsing = $levelSelect->getCreateOptionUsing();

    expect($createOptionUsing)->not->toBeNull();

    $createdId = $createOptionUsing(['name' => 'Exam Prep']);
    $createdLevel = Level::query()->find($createdId);

    expect($createdLevel)->not->toBeNull();
    expect($createdLevel?->name)->toBe('Exam Prep');
    expect($createdLevel?->slug)->toBe('exam-prep-1');
});
