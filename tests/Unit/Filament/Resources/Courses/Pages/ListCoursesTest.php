<?php

use App\Filament\Resources\Courses\Pages\ListCourses;
use Filament\Actions\CreateAction;

test('list courses page defines header actions', function (): void {
    $page = new ListCourses;

    $actions = invokeProtectedMethod($page, 'getHeaderActions');

    expect(actionClassList($actions))->toEqual([
        CreateAction::class,
    ]);
});
