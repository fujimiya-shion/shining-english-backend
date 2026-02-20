<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Models\OrderItem;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.name')
                    ->label('Course')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price')
                    ->money('VND')
                    ->sortable(),
                TextColumn::make('total')
                    ->getStateUsing(static fn (OrderItem $record): int => $record->price * $record->quantity)
                    ->money('VND')
                    ->sortable(),
            ]);
    }
}
