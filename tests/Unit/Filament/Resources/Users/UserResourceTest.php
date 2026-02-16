<?php

use App\Filament\Resources\Users\UserResource;
use App\Models\User;

it('uses user model and title attribute', function (): void {
    expect(UserResource::getModel())->toBe(User::class);
    expect(UserResource::getRecordTitleAttribute())->toBe('Account');
});

it('declares expected pages', function (): void {
    $pages = UserResource::getPages();

    expect($pages)->toHaveKeys(['index', 'create', 'edit']);
});

it('provides schema and table', function (): void {
    $schema = UserResource::form(makeSchema());
    $table = UserResource::table(makeTable());

    expect($schema)->toBeInstanceOf(Filament\Schemas\Schema::class);
    expect($table)->toBeInstanceOf(Filament\Tables\Table::class);
});
