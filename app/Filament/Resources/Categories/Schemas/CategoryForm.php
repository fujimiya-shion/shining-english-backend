<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(12)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(6),
                        TextInput::make('slug')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Leave empty to auto-generate from name.')
                            ->columnSpan(6),
                    ]),
                Select::make('parent_id')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('No parent'),
            ]);
    }
}
