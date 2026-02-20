<?php

use App\Filament\Pages\Dashboard;

test('dashboard defines columns', function (): void {
    $page = new Dashboard;

    expect($page->getColumns())->toMatchArray([
        'default' => 1,
        'md' => 6,
        'xl' => 12,
    ]);
});

test('dashboard has a custom title', function (): void {
    $page = new Dashboard;

    expect($page->getTitle())->toBe('Admin Dashboard');
});
