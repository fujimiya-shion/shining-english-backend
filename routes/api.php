<?php

use App\Http\Controllers\Api\V1\Course\CourseController;
use App\Http\Controllers\Api\V1\Lesson\LessonController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {
    Route::controller(CourseController::class)
        ->prefix('/courses')
        ->group(function () {
            Route::match(
                ['get', 'post'],
                '/filter',
                'filter',
            );
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
        });

    Route::controller(LessonController::class)
        ->prefix('/lessons')
        ->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::get('/{id}/quiz', 'quiz');
        });
});
