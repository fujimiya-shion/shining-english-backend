<?php

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Quizzes\QuizResource;
use App\Models\Quiz;
use App\Services\Quiz\IQuizService;

test('quiz resource extends base resource', function (): void {
    expect(is_subclass_of(QuizResource::class, BaseResource::class))->toBeTrue();
});

test('quiz resource uses quiz model and title attribute', function (): void {
    expect(QuizResource::getModel())->toBe(Quiz::class);
    expect(QuizResource::getRecordTitleAttribute())->toBe('lesson_id');
});

test('quiz resource defines expected pages', function (): void {
    $pages = QuizResource::getPages();

    expect($pages)->toHaveKeys(['index', 'create', 'edit']);
});

test('quiz resource configures form and table', function (): void {
    $schema = QuizResource::form(makeSchema());
    $table = QuizResource::table(makeTable());

    expect($schema)->toBeInstanceOf(\Filament\Schemas\Schema::class);
    expect($table)->toBeInstanceOf(\Filament\Tables\Table::class);
});

test('quiz resource builds record route binding query', function (): void {
    $query = QuizResource::getRecordRouteBindingEloquentQuery();

    expect($query)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
});

test('quiz resource resolves the quiz service', function (): void {
    $resource = new QuizResource;

    $service = invokeProtectedMethod($resource, 'service');

    expect($service)->toBeInstanceOf(IQuizService::class);
});
