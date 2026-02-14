<?php

use App\Filament\Resources\Lessons\Schemas\LessonForm;
use App\Models\Lesson;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Set;

test('lesson form defines expected components', function (): void {
    $schema = LessonForm::configure(makeSchema()->model(Lesson::class));

    $components = schemaComponentMap($schema);

    expect(array_keys($components))->toEqual([
        'name',
        'slug',
        'course_id',
        'video_url',
        'star_reward_video',
        'star_reward_quiz',
        'has_quiz',
        'pass_percent',
    ]);

    expect($components['name'])->toBeInstanceOf(TextInput::class);
    expect($components['slug'])->toBeInstanceOf(TextInput::class);
    expect($components['course_id'])->toBeInstanceOf(Select::class);
    expect($components['video_url'])->toBeInstanceOf(FileUpload::class);
    expect($components['star_reward_video'])->toBeInstanceOf(TextInput::class);
    expect($components['star_reward_quiz'])->toBeInstanceOf(TextInput::class);
    expect($components['has_quiz'])->toBeInstanceOf(Toggle::class);
});

test('lesson form marks required fields', function (): void {
    $schema = LessonForm::configure(makeSchema()->model(Lesson::class));

    $components = schemaComponentMap($schema);

    expect($components['name']->isRequired())->toBeTrue();
    expect($components['course_id']->isRequired())->toBeTrue();
    expect($components['video_url']->isRequired())->toBeTrue();
    expect($components['pass_percent']->isRequired())->toBeTrue();
});

test('lesson form configures numeric star inputs', function (): void {
    $schema = LessonForm::configure(makeSchema()->model(Lesson::class));

    $components = schemaComponentMap($schema);

    expect($components['star_reward_video']->isNumeric())->toBeTrue();
    expect($components['star_reward_quiz']->isNumeric())->toBeTrue();
});

test('lesson form toggles quiz state when has_quiz changes', function (): void {
    $schema = LessonForm::configure(makeSchema()->model(Lesson::class));

    $components = schemaComponentMap($schema);
    /** @var \Filament\Forms\Components\Toggle $toggle */
    $toggle = $components['has_quiz'];

    $rules = getProtectedPropertyValue($toggle, 'afterStateUpdated');
    $hook = $rules[0] ?? null;

    expect($hook)->toBeInstanceOf(Closure::class);

    $state = [];
    $set = new class($toggle, $state) extends Set
    {
        public function __construct(Component $component, public array &$state)
        {
            parent::__construct($component);
        }

        public function __invoke(string | Component $path, mixed $value, bool $isAbsolute = false, bool $shouldCallUpdatedHooks = false): mixed
        {
            $this->state[$path] = $value;
            return $value;
        }
    };

    $hook($set, true);
    expect($state['quiz.pass_percent'])->toBe(80);

    $hook($set, false);
    expect($state['quiz'])->toBeNull();
});
