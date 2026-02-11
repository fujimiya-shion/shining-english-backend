<?php

use App\Filament\Resources\Categories\Schemas\CategoryForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

it('builds name slug and parent fields', function (): void {
    $schema = CategoryForm::configure(makeSchema());

    $components = $schema->getComponents(withActions: false, withHidden: true);

    $parent = collect($components)->first(
        fn (object $component): bool => method_exists($component, 'getName') && $component->getName() === 'parent_id'
    );
    expect($parent)->toBeInstanceOf(Select::class);

    $grid = collect($components)->first(fn (object $component): bool => $component instanceof Grid);
    expect($grid)->toBeInstanceOf(Grid::class);

    $rawChildComponents = invokeProtectedMethod($grid, 'getDefaultChildComponents');
    $gridSchema = $rawChildComponents instanceof Schema ? $rawChildComponents : Schema::make()->components($rawChildComponents);
    $gridChildren = $gridSchema->getComponents(withActions: false, withHidden: true);

    $childNames = array_values(array_filter(array_map(
        fn (object $child): ?string => method_exists($child, 'getName') ? $child->getName() : null,
        $gridChildren,
    )));

    expect($childNames)->toEqual(['name', 'slug']);
    expect($gridChildren[0] ?? null)->toBeInstanceOf(TextInput::class);
    expect($gridChildren[1] ?? null)->toBeInstanceOf(TextInput::class);
});
