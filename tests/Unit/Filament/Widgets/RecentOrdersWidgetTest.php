<?php

use App\Filament\Widgets\RecentOrdersWidget;

test('recent orders widget defines table columns', function (): void {
    $widget = new RecentOrdersWidget;

    $table = $widget->table(makeTable());

    expect(tableColumnNames($table))->toEqual([
        'id',
        'user.name',
        'total_amount',
        'status',
        'placed_at',
    ]);
});
