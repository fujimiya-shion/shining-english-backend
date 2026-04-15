<?php

namespace App\Filament\Resources\Lessons\Schemas;

use App\Util\Video\VideoMetadataReader;
use App\Util\Php\PhpUploadLimit;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                            ->columnSpan(6),
                        TextInput::make('group_name')
                            ->label('Group')
                            ->maxLength(255)
                            ->placeholder('VD: Fundamentals of English')
                            ->columnSpan(6),
                        FileUpload::make('video_url')
                            ->key('lesson-video-upload')
                            ->label('Video')
                            ->required()
                            ->acceptedFileTypes([
                                'video/mp4',
                                'video/quicktime',
                                'video/x-msvideo',
                                'video/x-ms-wmv',
                                'video/x-matroska',
                                'video/webm',
                            ])
                            ->maxSize(PhpUploadLimit::maxKilobytes())
                            ->disk('local')
                            ->directory('lessons')
                            ->extraAttributes(['class' => 'lesson-video-upload'])
                            ->columnSpan(8)
                            ->live()
                            ->afterStateUpdated(function (Set $set, mixed $state): void {
                                if (! $state) {
                                    $set('duration_minutes', null);

                                    return;
                                }

                                $videoMetadataReader = app(VideoMetadataReader::class);
                                $minutes = null;

                                $resolvedState = is_array($state) ? reset($state) : $state;

                                if (is_object($resolvedState) && method_exists($resolvedState, 'getRealPath')) {
                                    $minutes = $videoMetadataReader
                                        ->detectDurationMinutesFromAbsolutePath($resolvedState->getRealPath());
                                } elseif (is_string($resolvedState)) {
                                    $minutes = $videoMetadataReader->detectDurationMinutes($resolvedState, 'local');
                                }

                                $set('duration_minutes', $minutes);
                            }),
                        FileUpload::make('documents')
                            ->key('lesson-documents-upload')
                            ->label('Tài liệu bài học')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-powerpoint',
                                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                'text/plain',
                                'application/zip',
                                'application/x-zip-compressed',
                            ])
                            ->rules([
                                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,text/plain,application/zip,application/x-zip-compressed',
                            ])
                            ->maxSize(PhpUploadLimit::maxKilobytes())
                            ->disk('local')
                            ->directory('lesson-documents')
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->downloadable()
                            ->openable()
                            ->storeFileNamesIn('document_names')
                            ->helperText('Upload tài liệu đính kèm cho bài học. Có thể kéo thả để đổi thứ tự.')
                            ->columnSpan(8),
                        KeyValue::make('document_names')
                            ->label('Tên hiển thị tài liệu')
                            ->keyLabel('File đã upload')
                            ->valueLabel('Tên hiển thị')
                            ->editableKeys(false)
                            ->editableValues(true)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->helperText('Mặc định dùng tên file gốc. Có thể sửa tên hiển thị hoặc tên tải xuống tại đây.')
                            ->columnSpan(4),
                        TextInput::make('duration_minutes')
                            ->label('Duration (minutes)')
                            ->numeric()
                            ->minValue(1)
                            ->readOnly()
                            ->helperText('Tự động lấy từ metadata của video khi upload.')
                            ->columnSpan(4),
                        Textarea::make('description')
                            ->rows(4)
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
