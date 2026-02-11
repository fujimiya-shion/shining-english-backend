<?php

use App\Filament\Resources\Categories\CategoryResource;
use App\Models\Category;

it('uses category model and proper title attribute', function (): void {
    expect(CategoryResource::getModel())->toBe(Category::class);
    expect(CategoryResource::getRecordTitleAttribute())->toBe('name');
});

it('declares expected pages', function (): void {
    $pages = CategoryResource::getPages();

    expect($pages)->toHaveKeys(['index', 'create', 'edit']);
});

it('provides schema and table', function (): void {
    $schema = CategoryResource::form(makeSchema());
    $table = CategoryResource::table(makeTable());

    expect($schema)->toBeInstanceOf(Filament\Schemas\Schema::class);
    expect($table)->toBeInstanceOf(Filament\Tables\Table::class);
});
