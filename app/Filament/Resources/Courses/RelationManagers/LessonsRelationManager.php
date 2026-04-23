<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Filament\Resources\Lessons\Schemas\LessonForm;
use App\Models\Lesson;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LessonsRelationManager extends RelationManager
{
    protected static string $relationship = 'lessons';

    public function form(Schema $schema): Schema
    {
        return LessonForm::configure($schema, withCourseField: false);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('group_order')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->orderBy('group_order')
                ->orderBy('lesson_order')
                ->orderBy('id'))
            ->columns([
                TextInputColumn::make('group_order')
                    ->label('Group Order')
                    ->type('number')
                    ->inputMode('numeric')
                    ->rules(['required', 'integer', 'min:1'])
                    ->width('8rem')
                    ->sortable()
                    ->afterStateUpdated(function (Lesson $record, mixed $state): void {
                        $groupOrder = max(1, (int) $state);

                        Lesson::withTrashed()
                            ->where('course_id', $record->course_id)
                            ->where('group_name', $record->group_name)
                            ->update(['group_order' => $groupOrder]);
                    }),
                TextColumn::make('lesson_order')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('group_name')
                    ->label('Group')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->formatStateUsing(fn (?int $state): string => $state ? "{$state}m" : '-')
                    ->sortable(),
                IconColumn::make('has_quiz')
                    ->label('Quiz')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('has_quiz'),
                TrashedFilter::make(),
            ])
            ->reorderable('lesson_order')
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $courseId = (int) $this->getOwnerRecord()->id;
                        $groupName = trim((string) ($data['group_name'] ?? ''));

                        $existingGroupOrder = Lesson::withTrashed()
                            ->where('course_id', $courseId)
                            ->where('group_name', $groupName)
                            ->value('group_order');

                        $nextGroupOrder = (int) Lesson::withTrashed()
                            ->where('course_id', $courseId)
                            ->max('group_order') + 1;

                        $groupOrder = max(
                            1,
                            (int) ($data['group_order'] ?? $existingGroupOrder ?? $nextGroupOrder),
                        );

                        $nextLessonOrder = (int) Lesson::withTrashed()
                            ->where('course_id', $courseId)
                            ->where('group_name', $groupName)
                            ->max('lesson_order') + 1;

                        return [
                            ...$data,
                            'course_id' => $courseId,
                            'group_order' => $groupOrder,
                            'lesson_order' => max(1, (int) ($data['lesson_order'] ?? $nextLessonOrder)),
                        ];
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->mutateDataUsing(function (array $data, Lesson $record): array {
                        $courseId = (int) $record->course_id;
                        $groupName = trim((string) ($data['group_name'] ?? $record->group_name ?? ''));

                        $existingGroupOrder = Lesson::withTrashed()
                            ->where('course_id', $courseId)
                            ->where('group_name', $groupName)
                            ->where('id', '!=', $record->id)
                            ->value('group_order');

                        $nextGroupOrder = (int) Lesson::withTrashed()
                            ->where('course_id', $courseId)
                            ->max('group_order') + 1;

                        return [
                            ...$data,
                            'group_order' => max(
                                1,
                                (int) ($data['group_order'] ?? $existingGroupOrder ?? $nextGroupOrder),
                            ),
                            'lesson_order' => max(1, (int) ($data['lesson_order'] ?? $record->lesson_order ?? 1)),
                        ];
                    }),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ]);
    }
}
