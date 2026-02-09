<?php

use App\Models\City;
use Illuminate\Database\Eloquent\Relations\HasMany;

it('defines fillable attributes', function (): void {
    $model = new City;

    expect($model->getFillable())->toEqual([
        'name',
        'sort_order',
    ]);
});

it('defines users relation', function (): void {
    $method = new ReflectionMethod(City::class, 'users');

    expect($method->getReturnType()?->getName())->toBe(HasMany::class);
});
