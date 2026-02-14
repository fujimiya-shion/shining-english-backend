<?php

use App\Filament\Resources\Lessons\RelationManagers\QuizRelationManager;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\TrashedFilter;

test('lesson quiz relation manager defines form components', function (): void {
    $manager = new QuizRelationManager;

    $schema = $manager->form(makeSchema());
    $components = schemaComponentMap($schema);

    expect($components['pass_percent'])->toBeInstanceOf(TextInput::class);
});

test('lesson quiz relation manager defines table configuration', function (): void {
    $manager = new QuizRelationManager;

    $table = $manager->table(makeTable());

    expect(tableColumnNames($table))->toEqual([
        'lesson.name',
        'pass_percent',
        'deleted_at',
        'created_at',
        'updated_at',
    ]);
});

test('lesson quiz relation manager registers filters and actions', function (): void {
    $manager = new QuizRelationManager;

    $table = $manager->table(makeTable());

    $filters = array_values($table->getFilters());
    expect($filters)->toHaveCount(1);
    expect($filters[0])->toBeInstanceOf(TrashedFilter::class);

    $headerActions = $table->getHeaderActions();
    expect(actionClassList($headerActions))->toEqual([CreateAction::class]);

    $rowActions = $table->getActions();
    expect(actionClassList($rowActions))->toEqual([
        EditAction::class,
        DeleteAction::class,
        RestoreAction::class,
        ForceDeleteAction::class,
    ]);
});
