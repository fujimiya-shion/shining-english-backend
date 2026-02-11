<?php

use App\Filament\Resources\Categories\Tables\CategoriesTable;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;

it('registers expected columns', function (): void {
    $table = CategoriesTable::configure(makeTable());

    expect(tableColumnNames($table))->toEqual([
        'name',
        'slug',
        'parent.name',
        'courses_count',
    ]);
});

it('binds trashed filter', function (): void {
    $table = CategoriesTable::configure(makeTable());

    $filters = array_values($table->getFilters());

    expect(actionClassList($filters))->toContain(TrashedFilter::class);
});

it('adds record action + bulk toolbar actions', function (): void {
    $table = CategoriesTable::configure(makeTable());

    $recordActions = $table->getRecordActions();
    expect(actionClassList($recordActions))->toEqual([EditAction::class]);

    $toolbarActions = $table->getToolbarActions();
    expect(actionClassList($toolbarActions))->toEqual([
        DeleteBulkAction::class,
        ForceDeleteBulkAction::class,
        RestoreBulkAction::class,
    ]);
});
