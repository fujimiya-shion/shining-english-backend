<?php

namespace App\Filament\Resources\Quizzes\RelationManagers;

use Closure;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(12)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('content')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(12),
                        Repeater::make('answers')
                            ->relationship()
                            ->schema([
                                TextInput::make('content')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(9),
                                Toggle::make('is_correct')
                                    ->label('Correct')
                                    ->inline(false)
                                    ->columnSpan(3),
                            ])
                            ->minItems(2)
                            ->defaultItems(2)
                            ->rule(function (string $attribute, $value, Closure $fail): void {
                                $answers = collect($value ?? []);
                                if (! $answers->contains(fn ($answer) => ($answer['is_correct'] ?? false) === true)) {
                                    $fail('At least one answer must be marked correct.');
                                }
                            })
                            ->columnSpan(12),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('content')
                    ->searchable()
                    ->limit(80),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ]);
    }
}
