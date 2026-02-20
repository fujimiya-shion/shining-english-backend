<?php

use App\Filament\Resources\Orders\RelationManagers\ItemsRelationManager;

test('order items relation manager is read only', function (): void {
    $manager = new ItemsRelationManager;

    expect($manager->isReadOnly())->toBeTrue();
});

test('order items relation manager defines table configuration', function (): void {
    $manager = new ItemsRelationManager;

    $table = $manager->table(makeTable());

    expect(tableColumnNames($table))->toEqual([
        'course.name',
        'quantity',
        'price',
        'total',
    ]);
});

test('order items relation manager registers no filters or actions', function (): void {
    $manager = new ItemsRelationManager;

    $table = $manager->table(makeTable());

    expect($table->getFilters())->toBeEmpty();
    expect($table->getHeaderActions())->toBeEmpty();
    expect($table->getActions())->toBeEmpty();
});
