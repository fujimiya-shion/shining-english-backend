<?php

use App\Filament\Resources\Categories\Pages\ListCategories;
use Filament\Actions\CreateAction;

it('list categories page defines header actions', function (): void {
    $page = new ListCategories;

    $actions = invokeProtectedMethod($page, 'getHeaderActions');

    expect(actionClassList($actions))->toEqual([
        CreateAction::class,
    ]);
});
