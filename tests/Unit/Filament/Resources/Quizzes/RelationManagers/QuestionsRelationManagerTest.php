<?php

use App\Filament\Resources\Quizzes\RelationManagers\QuestionsRelationManager;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\TrashedFilter;

test('questions relation manager defines form components', function (): void {
    $manager = new QuestionsRelationManager;

    $schema = $manager->form(makeSchema());
    $components = schemaComponentMap($schema);

    expect($components['content'])->toBeInstanceOf(TextInput::class);
    expect($components['answers'])->toBeInstanceOf(Repeater::class);
});

test('questions relation manager defines table configuration', function (): void {
    $manager = new QuestionsRelationManager;

    $table = $manager->table(makeTable());

    expect(tableColumnNames($table))->toEqual([
        'content',
        'deleted_at',
        'created_at',
        'updated_at',
    ]);
});

test('questions relation manager registers filters and actions', function (): void {
    $manager = new QuestionsRelationManager;

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

test('answers repeater requires at least one correct answer', function (): void {
    $manager = new QuestionsRelationManager;

    $schema = $manager->form(makeSchema());
    $components = schemaComponentMap($schema);

    /** @var Repeater $repeater */
    $repeater = $components['answers'];

    $rules = getProtectedPropertyValue($repeater, 'rules');
    $rule = null;

    foreach ($rules as $entry) {
        $candidate = $entry[0] ?? null;
        if (! $candidate instanceof Closure) {
            continue;
        }

        $params = (new ReflectionFunction($candidate))->getNumberOfParameters();
        if ($params === 3) {
            $rule = $candidate;
            break;
        }
    }

    expect($rule)->toBeInstanceOf(Closure::class);

    $failedMessage = null;
    $rule('answers', [
        ['content' => 'A', 'is_correct' => false],
        ['content' => 'B', 'is_correct' => false],
    ], function (string $message) use (&$failedMessage): void {
        $failedMessage = $message;
    });

    expect($failedMessage)->not->toBeNull();

    $failedMessage = null;
    $rule('answers', [
        ['content' => 'A', 'is_correct' => true],
        ['content' => 'B', 'is_correct' => false],
    ], function (string $message) use (&$failedMessage): void {
        $failedMessage = $message;
    });

    expect($failedMessage)->toBeNull();
});
