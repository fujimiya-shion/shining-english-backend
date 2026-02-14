<?php

namespace App\Filament\Resources\Quizzes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class QuizForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(12)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('lesson_id')
                            ->relationship('lesson', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(12),
                        TextInput::make('pass_percent')
                            ->label('Pass Percent')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(1)
                            ->default(80)
                            ->required()
                            ->columnSpan(4),
                    ]),
            ]);
    }
}
