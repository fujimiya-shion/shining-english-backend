<?php

use App\Filament\Resources\Courses\Tables\CoursesTable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Model;

test('courses table defines expected columns', function (): void {
    $table = CoursesTable::configure(makeTable());

    expect(tableColumnNames($table))->toEqual([
        'thumbnail',
        'name',
        'slug',
        'price',
        'allow_star_payment',
        'star_price',
        'status',
        'category.name',
        'level.name',
        'deleted_at',
        'created_at',
        'updated_at',
    ]);
});

test('courses table registers trashed filter', function (): void {
    $table = CoursesTable::configure(makeTable());

    $filters = $table->getFilters();
    $filters = array_values($filters);

    expect($filters)->toHaveCount(5);
    expect(actionClassList($filters))->toEqual([
        TernaryFilter::class,
        TernaryFilter::class,
        SelectFilter::class,
        SelectFilter::class,
        TrashedFilter::class,
    ]);
});

test('courses table registers record actions', function (): void {
    $table = CoursesTable::configure(makeTable());

    $actions = $table->getRecordActions();

    expect(actionClassList($actions))->toEqual([
        EditAction::class,
        \Filament\Actions\Action::class,
        \Filament\Actions\DeleteAction::class,
    ]);
});

test('courses table registers bulk action group', function (): void {
    $table = CoursesTable::configure(makeTable());

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

test('courses table duplicates records', function (): void {
    $table = CoursesTable::configure(makeTable());
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
    $record->name = 'IELTS Foundation';
    $record->slug = 'ielts-foundation';

    $actions[1]->getActionFunction()($record);

    expect($record::$saved?->name)->toBe('IELTS Foundation (Sao chép)');
    expect($record::$saved?->slug)->toBe('ielts-foundation-copy');
});
