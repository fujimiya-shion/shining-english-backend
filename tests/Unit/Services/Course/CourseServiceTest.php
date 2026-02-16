<?php
namespace Tests\Unit\Services\Course;

use App\Models\Course;
use App\Repositories\Course\CourseRepository;
use App\Services\Course\CourseService;
use App\Services\Course\ICourseService;
use App\Repositories\Course\ICourseRepository;
use App\ValueObjects\CourseFilter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

uses(TestCase::class);

it("implements shared service contract", function () {
    $model = new Course;
    $repository = new CourseRepository($model);
    $service = new CourseService($repository);
    assertServiceContract($service, ICourseService::class, $repository);
});

it('filters courses via repository', function (): void {
    $items = new Collection;
    $paginator = new LengthAwarePaginator($items, 0, 15, 1);

    $repository = Mockery::mock(ICourseRepository::class);
    $repository->shouldReceive('filter')
        ->once()
        ->with(
            Mockery::on(function (CourseFilter $filters): bool {
                return $filters->categoryId === 1
                    && $filters->priceMin === 100;
            }),
        )
        ->andReturn($paginator);

    $service = new CourseService($repository);

    $result = $service->filter(CourseFilter::fromArray([
        'category_id' => 1,
        'price_min' => 100,
    ]));

    expect($result)->toBe($paginator);
});
