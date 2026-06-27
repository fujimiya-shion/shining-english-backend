<?php

use App\Filament\Resources\Blogs\Pages\ListBlogs;
use Filament\Actions\CreateAction;

test('list blogs page defines header actions', function (): void {
    $page = new ListBlogs;

    expect(actionClassList(invokeProtectedMethod($page, 'getHeaderActions')))->toEqual([
        CreateAction::class,
    ]);
});
