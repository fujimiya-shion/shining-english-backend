<?php

use App\Traits\Slugable;
use Illuminate\Database\Eloquent\Model;

it('returns null when no slug source value exists', function (): void {
    $model = new class extends Model
    {
        use Slugable;

        protected $fillable = ['slug'];
    };

    $method = new ReflectionMethod($model::class, 'getSlugSourceValue');

    expect($method->invoke(null, $model))->toBeNull();
});
