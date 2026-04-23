<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Models\Lesson;
use App\Models\LessonGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LessonGroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'lessonGroups';

    protected static ?string $title = 'Lesson Groups';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lessons_count')
                    ->label('Lessons')
                    ->counts('lessons'),
            ])
            ->reorderable('sort_order')
            ->afterReordering(function (array $order): void {
                foreach (array_values($order) as $index => $groupId) {
                    $sortOrder = $index + 1;

                    Lesson::query()
                        ->where('lesson_group_id', (int) $groupId)
                        ->update(['group_order' => $sortOrder]);
                }
            })
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(fn (array $data): array => [
                        ...$data,
                        'course_id' => (int) $this->getOwnerRecord()->id,
                        'sort_order' => ((int) LessonGroup::query()
                            ->where('course_id', (int) $this->getOwnerRecord()->id)
                            ->max('sort_order')) + 1,
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }
}
