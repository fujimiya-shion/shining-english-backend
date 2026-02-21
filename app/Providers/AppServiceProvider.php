<?php

namespace App\Providers;

use App\Repositories\Cart\CartRepository;
use App\Repositories\Cart\ICartRepository;
use App\Repositories\Course\CourseRepository;
use App\Repositories\Course\ICourseRepository;
use App\Repositories\Enrollment\EnrollmentRepository;
use App\Repositories\Enrollment\IEnrollmentRepository;
use App\Repositories\Lesson\ILessonRepository;
use App\Repositories\Lesson\LessonRepository;
use App\Repositories\Order\IOrderRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\OrderItem\IOrderItemRepository;
use App\Repositories\OrderItem\OrderItemRepository;
use App\Repositories\Quiz\IQuizRepository;
use App\Repositories\Quiz\QuizRepository;
use App\Repositories\UserQuizAttempt\IUserQuizAttemptRepository;
use App\Repositories\UserQuizAttempt\UserQuizAttemptRepository;
use App\Repositories\User\IUserDeviceRepository;
use App\Repositories\User\IUserRepository;
use App\Repositories\User\UserDeviceRepository;
use App\Repositories\User\UserRepository;
use App\Services\Cart\CartService;
use App\Services\Cart\ICartService;
use App\Services\Course\CourseService;
use App\Services\Course\ICourseService;
use App\Services\Enrollment\EnrollmentService;
use App\Services\Enrollment\IEnrollmentService;
use App\Services\Lesson\ILessonService;
use App\Services\Lesson\LessonService;
use App\Services\Order\IOrderService;
use App\Services\Order\OrderService;
use App\Services\OrderItem\IOrderItemService;
use App\Services\OrderItem\OrderItemService;
use App\Services\Quiz\IQuizService;
use App\Services\Quiz\QuizService;
use App\Services\UserQuizAttempt\IUserQuizAttemptService;
use App\Services\UserQuizAttempt\UserQuizAttemptService;
use App\Services\User\IUserDeviceService;
use App\Services\User\IUserService;
use App\Services\User\UserDeviceService;
use App\Services\User\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ICartRepository::class, CartRepository::class);
        $this->app->bind(IEnrollmentRepository::class, EnrollmentRepository::class);
        $this->app->bind(IOrderRepository::class, OrderRepository::class);
        $this->app->bind(IOrderItemRepository::class, OrderItemRepository::class);
        $this->app->bind(ICourseRepository::class, CourseRepository::class);
        $this->app->bind(ILessonRepository::class, LessonRepository::class);
        $this->app->bind(IQuizRepository::class, QuizRepository::class);
        $this->app->bind(IUserQuizAttemptRepository::class, UserQuizAttemptRepository::class);
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IUserDeviceRepository::class, UserDeviceRepository::class);

        $this->app->bind(ICartService::class, CartService::class);
        $this->app->bind(IEnrollmentService::class, EnrollmentService::class);
        $this->app->bind(IOrderService::class, OrderService::class);
        $this->app->bind(IOrderItemService::class, OrderItemService::class);
        $this->app->bind(ICourseService::class, CourseService::class);
        $this->app->bind(ILessonService::class, LessonService::class);
        $this->app->bind(IQuizService::class, QuizService::class);
        $this->app->bind(IUserQuizAttemptService::class, UserQuizAttemptService::class);
        $this->app->bind(IUserService::class, UserService::class);
        $this->app->bind(IUserDeviceService::class, UserDeviceService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
