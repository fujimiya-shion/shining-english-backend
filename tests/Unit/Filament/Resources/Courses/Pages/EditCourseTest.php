<?php

use App\Filament\Resources\Courses\Pages\EditCourse;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;

test('edit course page defines header actions', function (): void {
    $page = new EditCourse;

    $actions = invokeProtectedMethod($page, 'getHeaderActions');

    expect(actionClassList($actions))->toEqual([
        DeleteAction::class,
        ForceDeleteAction::class,
        RestoreAction::class,
    ]);
});
