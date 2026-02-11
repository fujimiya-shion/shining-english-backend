<?php

use App\Filament\Resources\Courses\Tables\CoursesTable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;

test('courses table defines expected columns', function (): void {
    $table = CoursesTable::configure(makeTable());

    expect(tableColumnNames($table))->toEqual([
        'name',
        'slug',
        'price',
        'status',
        'thumbnail',
        'category.name',
        'deleted_at',
        'created_at',
        'updated_at',
    ]);
});

test('courses table registers trashed filter', function (): void {
    $table = CoursesTable::configure(makeTable());

    $filters = $table->getFilters();
    $filters = array_values($filters);

    expect($filters)->toHaveCount(1);
    expect($filters[0])->toBeInstanceOf(TrashedFilter::class);
});

test('courses table registers edit record action', function (): void {
    $table = CoursesTable::configure(makeTable());

    $actions = $table->getRecordActions();

    expect(actionClassList($actions))->toEqual([EditAction::class]);
});

test('courses table registers bulk action group', function (): void {
    $table = CoursesTable::configure(makeTable());

    $toolbarActions = $table->getToolbarActions();

    expect($toolbarActions)->toHaveCount(1);
    expect($toolbarActions[0])->toBeInstanceOf(BulkActionGroup::class);

    $groupActions = $toolbarActions[0]->getActions();

    expect(actionClassList($groupActions))->toEqual([
        DeleteBulkAction::class,
        ForceDeleteBulkAction::class,
        RestoreBulkAction::class,
    ]);
});
