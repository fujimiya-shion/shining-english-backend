<?php

use App\Filament\Resources\Blogs\BlogResource;
use App\Models\Blog;
use Illuminate\Database\Eloquent\Builder;

test('blog resource uses blog model and title attribute', function (): void {
    expect(BlogResource::getModel())->toBe(Blog::class);
    expect(BlogResource::getRecordTitleAttribute())->toBe('title');
});

test('blog resource defines expected pages and no relations', function (): void {
    expect(BlogResource::getRelations())->toBe([]);
    expect(BlogResource::getPages())->toHaveKeys(['index', 'create', 'edit']);
});

test('blog resource configures form table and query', function (): void {
    expect(BlogResource::form(makeSchema()))->toBeInstanceOf(\Filament\Schemas\Schema::class);
    expect(BlogResource::table(makeTable()))->toBeInstanceOf(\Filament\Tables\Table::class);
    expect(BlogResource::getEloquentQuery())->toBeInstanceOf(Builder::class);
});
