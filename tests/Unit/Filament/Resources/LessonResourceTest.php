<?php

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Lessons\LessonResource;
use App\Models\Lesson;
use App\Services\Lesson\ILessonService;

test('lesson resource extends base resource', function (): void {
    expect(is_subclass_of(LessonResource::class, BaseResource::class))->toBeTrue();
});

test('lesson resource uses lesson model and title attribute', function (): void {
    expect(LessonResource::getModel())->toBe(Lesson::class);
    expect(LessonResource::getRecordTitleAttribute())->toBe('name');
});

test('lesson resource defines expected pages', function (): void {
    $pages = LessonResource::getPages();

    expect($pages)->toHaveKeys(['index', 'create', 'edit']);
});

test('lesson resource configures form and table', function (): void {
    $schema = LessonResource::form(makeSchema());
    $table = LessonResource::table(makeTable());

    expect($schema)->toBeInstanceOf(\Filament\Schemas\Schema::class);
    expect($table)->toBeInstanceOf(\Filament\Tables\Table::class);
});

test('lesson resource builds record route binding query', function (): void {
    $query = LessonResource::getRecordRouteBindingEloquentQuery();

    expect($query)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
});

test('lesson resource resolves the lesson service', function (): void {
    $resource = new LessonResource;

    $service = invokeProtectedMethod($resource, 'service');

    expect($service)->toBeInstanceOf(ILessonService::class);
});
