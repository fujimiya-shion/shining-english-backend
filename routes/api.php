<?php

use App\Http\Controllers\Api\V1\Cart\CartController;
use App\Http\Controllers\Api\V1\Course\CourseController;
use App\Http\Controllers\Api\V1\Lesson\LessonController;
use App\Http\Controllers\Api\V1\QuizAttempt\QuizAttemptController;
use App\Http\Controllers\Api\V1\Transaction\OrderController;
use App\Http\Controllers\Api\V1\User\AuthController;
use App\Http\Controllers\Api\V1\User\UserController;
use App\Http\Middleware\VerifyUserToken;
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

    Route::controller(AuthController::class)
        ->prefix('/auth')
        ->group(function () {
            Route::post('/register', 'register');
            Route::post('/login', 'login');
        });

    Route::middleware(VerifyUserToken::class)
        ->controller(AuthController::class)
        ->prefix('/auth')
        ->group(function () {
            Route::get('/me', 'me');
            Route::post('/logout', 'logout');
        });

    Route::middleware(VerifyUserToken::class)
        ->controller(UserController::class)
        ->prefix('/user')
        ->group(function () {
            Route::post('/update', 'update');
        });

    Route::middleware(VerifyUserToken::class)
        ->controller(QuizAttemptController::class)
        ->prefix('/quizzes/{quizId}/attempts')
        ->group(function () {
            Route::get('/', 'index');
            Route::get('/latest', 'latest');
            Route::post('/', 'store');
        });

    Route::middleware(VerifyUserToken::class)
        ->controller(CartController::class)
        ->prefix('/cart')
        ->group(function () {
            Route::get('/items', 'items');
            Route::get('/count', 'count');
            Route::delete('/clear', 'clear');
        });

    Route::middleware(VerifyUserToken::class)
        ->controller(OrderController::class)
        ->prefix('/orders')
        ->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::post('/{id}/cancel', 'cancel');
        });
});
