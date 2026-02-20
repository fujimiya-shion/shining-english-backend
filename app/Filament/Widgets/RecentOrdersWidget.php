<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentOrdersWidget extends TableWidget
{
    protected int|string|array $columnSpan = [
        'default' => 12,
        'md' => 12,
        'xl' => 12,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->with('user')
                    ->latest('placed_at'),
            )
            ->columns([
                TextColumn::make('id')
                    ->label('Order')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('VND')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(static fn (OrderStatus $state): string => strtoupper($state->value)),
                TextColumn::make('placed_at')
                    ->label('Placed At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(5)
            ->paginationPageOptions([5, 10, 25]);
    }
}
