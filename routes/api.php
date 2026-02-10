<?php

use App\Http\Controllers\Api\V1\Course\CourseController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {
    Route::controller(CourseController::class)
        ->prefix('/courses')
        ->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
        });
});
