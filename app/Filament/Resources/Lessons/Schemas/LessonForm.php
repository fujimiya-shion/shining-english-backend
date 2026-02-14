<?php

namespace App\Filament\Resources\Lessons\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LessonForm
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
                            ->columnSpan(8),
                        TextInput::make('slug')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Leave empty to auto-generate from name.')
                            ->columnSpan(4),
                        Select::make('course_id')
                            ->relationship('course', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(12),
                        FileUpload::make('video_url')
                            ->label('Video')
                            ->required()
                            ->acceptedFileTypes(['video/*'])
                            ->disk('local')
                            ->directory('lessons')
                            ->extraAttributes(['class' => 'lesson-video-upload'])
                            ->columnSpan(12),
                        TextInput::make('star_reward_video')
                            ->numeric()
                            ->minValue(0)
                            ->step(1)
                            ->default(0)
                            ->columnSpan(4),
                        TextInput::make('star_reward_quiz')
                            ->numeric()
                            ->minValue(0)
                            ->step(1)
                            ->default(0)
                            ->columnSpan(4),
                        Toggle::make('has_quiz')
                            ->inline(false)
                            ->default(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?bool $state): void {
                                if ($state) {
                                    $set('quiz.pass_percent', 80);
                                } else {
                                    $set('quiz', null);
                                }
                            })
                            ->columnSpan(4),
                    ]),
                Section::make('Quiz')
                    ->relationship('quiz')
                    ->visible(fn (Get $get): bool => (bool) $get('has_quiz'))
                    ->schema([
                        TextInput::make('pass_percent')
                            ->label('Pass Percent')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(1)
                            ->default(80)
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
