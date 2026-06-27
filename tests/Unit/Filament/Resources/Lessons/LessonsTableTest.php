<?php

use App\Filament\Resources\Lessons\Tables\LessonsTable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Model;

test('lessons table defines expected columns', function (): void {
    $table = LessonsTable::configure(makeTable());

    expect(tableColumnNames($table))->toEqual([
        'name',
        'slug',
        'course.name',
        'group_name',
        'group_order',
        'lesson_order',
        'duration_minutes',
        'video_url',
        'description',
        'star_reward_video',
        'star_reward_quiz',
        'has_quiz',
        'deleted_at',
        'created_at',
        'updated_at',
    ]);
});

test('lessons table registers filters', function (): void {
    $table = LessonsTable::configure(makeTable());

    $filters = array_values($table->getFilters());

    expect($filters)->toHaveCount(3);
    expect($filters[0])->toBeInstanceOf(TernaryFilter::class);
    expect($filters[1])->toBeInstanceOf(SelectFilter::class);
    expect($filters[2])->toBeInstanceOf(TrashedFilter::class);
});

test('lessons table registers record actions', function (): void {
    $table = LessonsTable::configure(makeTable());

    $actions = $table->getRecordActions();

    expect(actionClassList($actions))->toEqual([
        EditAction::class,
        \Filament\Actions\Action::class,
        \Filament\Actions\DeleteAction::class,
    ]);
});

test('lessons table registers bulk action group', function (): void {
    $table = LessonsTable::configure(makeTable());

    $toolbarActions = $table->getToolbarActions();

    expect($toolbarActions)->toHaveCount(1);
    expect($toolbarActions[0])->toBeInstanceOf(BulkActionGroup::class);

    $groupActions = $toolbarActions[0]->getActions();

    expect(actionClassList($groupActions))->toEqual([
        DeleteBulkAction::class,
        ForceDeleteBulkAction::class,
        RestoreBulkAction::class,
    ]);
});

test('lessons table duplicates records', function (): void {
    $table = LessonsTable::configure(makeTable());
    $actions = $table->getRecordActions();

    $record = new class extends Model
    {
        public static ?Model $saved = null;

        public $timestamps = false;

        protected $guarded = [];

        public function save(array $options = []): bool
        {
            self::$saved = $this;

            return true;
        }
    };
    $record->name = 'Lesson one';
    $record->slug = 'lesson-one';

    $actions[1]->getActionFunction()($record);

    expect($record::$saved?->name)->toBe('Lesson one (Sao chép)');
    expect($record::$saved?->slug)->toBe('lesson-one-copy');
});
