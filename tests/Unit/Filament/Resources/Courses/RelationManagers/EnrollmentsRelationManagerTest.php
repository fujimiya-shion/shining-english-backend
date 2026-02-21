<?php

use App\Filament\Resources\Courses\RelationManagers\EnrollmentsRelationManager;
use Filament\Tables\Filters\TrashedFilter;

test('course enrollments relation manager is read only', function (): void {
    $manager = new EnrollmentsRelationManager;

    expect($manager->isReadOnly())->toBeTrue();
});

test('course enrollments relation manager defines table configuration', function (): void {
    $manager = new EnrollmentsRelationManager;

    $table = $manager->table(makeTable());

    expect(tableColumnNames($table))->toEqual([
        'user.name',
        'user.email',
        'order_id',
        'enrolled_at',
        'created_at',
        'deleted_at',
    ]);
});

test('course enrollments relation manager registers trashed filter', function (): void {
    $manager = new EnrollmentsRelationManager;

    $table = $manager->table(makeTable());

    $filters = array_values($table->getFilters());
    expect($filters)->toHaveCount(1);
    expect($filters[0])->toBeInstanceOf(TrashedFilter::class);
});
