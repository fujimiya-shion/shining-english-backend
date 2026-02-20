<?php

use App\Filament\Resources\Users\Pages\ListUsers;
use Filament\Actions\CreateAction;

test('list users page defines header actions', function (): void {
    $page = new ListUsers;

    $actions = invokeProtectedMethod($page, 'getHeaderActions');

    expect(actionClassList($actions))->toEqual([
        CreateAction::class,
    ]);
});
