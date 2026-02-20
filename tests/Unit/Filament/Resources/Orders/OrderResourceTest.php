<?php

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;

test('order resource uses order model and title attribute', function (): void {
    expect(OrderResource::getModel())->toBe(Order::class);
    expect(OrderResource::getRecordTitleAttribute())->toBe('id');
});

test('order resource defines expected pages', function (): void {
    $pages = OrderResource::getPages();

    expect($pages)->toHaveKeys(['index', 'view']);
});

test('order resource configures form table and infolist', function (): void {
    $schema = OrderResource::form(makeSchema());
    $table = OrderResource::table(makeTable());
    $infolist = OrderResource::infolist(makeSchema());

    expect($schema)->toBeInstanceOf(\Filament\Schemas\Schema::class);
    expect($table)->toBeInstanceOf(\Filament\Tables\Table::class);
    expect($infolist)->toBeInstanceOf(\Filament\Schemas\Schema::class);

    $components = schemaComponentMap($infolist);
    expect($components)->toHaveKeys([
        'id',
        'user.name',
        'user.email',
        'total_amount',
        'status',
        'payment_method',
        'placed_at',
        'created_at',
        'updated_at',
    ]);
});

test('order resource builds query with eager loads', function (): void {
    $query = OrderResource::getEloquentQuery();

    expect($query)->toBeInstanceOf(Builder::class);

    $eagerLoads = $query->getEagerLoads();
    expect($eagerLoads)->toHaveKey('user');
    expect($eagerLoads)->toHaveKey('items.course');
});
