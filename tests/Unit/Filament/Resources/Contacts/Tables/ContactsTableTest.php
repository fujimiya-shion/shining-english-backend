<?php

use App\Filament\Resources\Contacts\Tables\ContactsTable;
use Filament\Actions\EditAction;
use Illuminate\Database\Eloquent\Model;

test('contacts table defines expected columns', function (): void {
    $table = ContactsTable::configure(makeTable());

    expect(tableColumnNames($table))->toEqual([
        'id',
        'name',
        'email',
        'message',
        'replied_at',
        'created_at',
    ]);
});

test('contacts table registers record actions', function (): void {
    $table = ContactsTable::configure(makeTable());

    $actions = $table->getRecordActions();

    expect(actionClassList($actions))->toEqual([
        EditAction::class,
        \Filament\Actions\Action::class,
        \Filament\Actions\DeleteAction::class,
    ]);
});

test('contacts table duplicates records', function (): void {
    $table = ContactsTable::configure(makeTable());
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
    $record->name = 'Learner';
    $record->email = 'learner@example.com';

    $actions[1]->getActionFunction()($record);

    expect($record::$saved?->name)->toBe('Learner');
    expect($record::$saved?->email)->toBe('learner@example.com');
});
