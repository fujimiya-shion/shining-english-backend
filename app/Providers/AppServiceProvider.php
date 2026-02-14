<?php

namespace App\Providers;

use App\Repositories\Course\CourseRepository;
use App\Repositories\Course\ICourseRepository;
use App\Repositories\Lesson\ILessonRepository;
use App\Repositories\Lesson\LessonRepository;
use App\Repositories\Quiz\IQuizRepository;
use App\Repositories\Quiz\QuizRepository;
use App\Services\Course\CourseService;
use App\Services\Course\ICourseService;
use App\Services\Lesson\ILessonService;
use App\Services\Lesson\LessonService;
use App\Services\Quiz\IQuizService;
use App\Services\Quiz\QuizService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ICourseRepository::class, CourseRepository::class);
        $this->app->bind(ILessonRepository::class, LessonRepository::class);
        $this->app->bind(IQuizRepository::class, QuizRepository::class);

        $this->app->bind(ICourseService::class, CourseService::class);
        $this->app->bind(ILessonService::class, LessonService::class);
        $this->app->bind(IQuizService::class, QuizService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
