<?php

use App\Filament\Resources\Quizzes\Pages\ListQuizzes;
use Filament\Actions\CreateAction;

test('list quizzes page defines header actions', function (): void {
    $page = new ListQuizzes;

    $actions = invokeProtectedMethod($page, 'getHeaderActions');

    expect(actionClassList($actions))->toEqual([
        CreateAction::class,
    ]);
});
