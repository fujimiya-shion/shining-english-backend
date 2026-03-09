<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Models\Level;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(12)
                    ->columnSpanFull()
                    ->schema([
                        Toggle::make('status')
                            ->required()
                            ->inline(false)
                            ->default(true)
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(8),
                        TextInput::make('slug')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Auto-generate from name if left unchanged.')
                            ->columnSpan(4),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(4),
                        Select::make('levels')
                            ->relationship('levels', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $name = trim((string) ($data['name'] ?? ''));
                                $baseSlug = Str::slug($name);
                                $slug = $baseSlug;
                                $index = 1;

                                while (Level::query()->where('slug', $slug)->exists()) {
                                    $slug = "{$baseSlug}-{$index}";
                                    $index++;
                                }

                                return Level::query()->create([
                                    'name' => $name,
                                    'slug' => $slug,
                                ])->getKey();
                            })
                            ->columnSpan(4),
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('VND')
                            ->minValue(0)
                            ->columnSpan(2),
                        TextInput::make('rating')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
                            ->step(0.1)
                            ->columnSpan(2),
                        TextInput::make('learned')
                            ->numeric()
                            ->minValue(0)
                            ->step(1)
                            ->columnSpan(2),
                        FileUpload::make('thumbnail')
                            ->image()
                            ->disk('public')
                            ->directory('courses')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(2048)
                            ->columnSpan(12),
                        RichEditor::make('description')
                            ->columnSpan(12),
                    ]),
            ]);
    }
}
