<?php

use App\Filament\Resources\Users\Pages\EditUser;
use Filament\Actions\DeleteAction;

test('edit user page defines header actions', function (): void {
    $page = new EditUser;

    $actions = invokeProtectedMethod($page, 'getHeaderActions');

    expect(actionClassList($actions))->toEqual([
        DeleteAction::class,
    ]);
});
