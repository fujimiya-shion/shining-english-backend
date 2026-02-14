<?php

namespace App\Filament\Resources\Lessons\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
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
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Auto-generate from name if left unchanged.')
                            ->columnSpan(4),
                        Select::make('course_id')
                            ->relationship('course', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(12),
                        FileUpload::make('video_url')
                            ->label('Video')
                            ->acceptedFileTypes(['video/*'])
                            ->disk('public')
                            ->directory('lessons')
                            ->visibility('public')
                            ->extraAttributes(['class' => 'lesson-video-upload'])
                            ->columnSpan(12),
                        TextInput::make('star_reward_video')
                            ->numeric()
                            ->minValue(0)
                            ->step(1)
                            ->columnSpan(4),
                        TextInput::make('star_reward_quiz')
                            ->numeric()
                            ->minValue(0)
                            ->step(1)
                            ->columnSpan(4),
                        Toggle::make('has_quiz')
                            ->inline(false)
                            ->columnSpan(4),
                    ]),
            ]);
    }
}
