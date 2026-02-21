<?php

namespace Tests\Unit\Services\Enrollment;

use App\Models\Enrollment;
use App\Repositories\Enrollment\EnrollmentRepository;
use App\Repositories\Enrollment\IEnrollmentRepository;
use App\Services\Enrollment\EnrollmentService;
use App\Services\Enrollment\IEnrollmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

it('implements shared service contract', function (): void {
    $model = new Enrollment;
    $repository = new EnrollmentRepository($model);
    $service = new EnrollmentService($repository);

    assertServiceContract($service, IEnrollmentService::class, $repository);
});

it('enrolls a user when missing', function (): void {
    $enrollment = new Enrollment;

    $repository = Mockery::mock(IEnrollmentRepository::class);
    $repository->shouldReceive('findByUserAndCourse')
        ->once()
        ->with(10, 20)
        ->andReturnNull();
    $repository->shouldReceive('create')
        ->once()
        ->with(Mockery::on(function (array $data): bool {
            return $data['user_id'] === 10
                && $data['course_id'] === 20
                && $data['order_id'] === 30
                && isset($data['enrolled_at']);
        }))
        ->andReturn($enrollment);

    $service = new EnrollmentService($repository);

    $result = $service->enroll(10, 20, 30);

    expect($result)->toBe($enrollment);
});

it('returns existing enrollment', function (): void {
    $enrollment = new Enrollment;

    $repository = Mockery::mock(IEnrollmentRepository::class);
    $repository->shouldReceive('findByUserAndCourse')
        ->once()
        ->with(10, 20)
        ->andReturn($enrollment);
    $repository->shouldReceive('create')->never();

    $service = new EnrollmentService($repository);

    $result = $service->enroll(10, 20);

    expect($result)->toBe($enrollment);
});

it('checks enrollment status', function (): void {
    $repository = Mockery::mock(IEnrollmentRepository::class);
    $repository->shouldReceive('findByUserAndCourse')
        ->once()
        ->with(10, 20)
        ->andReturn(new Enrollment);

    $service = new EnrollmentService($repository);

    expect($service->isEnrolled(10, 20))->toBeTrue();
});
