<?php

use App\ValueObjects\MetaPagination;
use App\ValueObjects\QueryOption;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Tests\TestCase;

uses(TestCase::class);

it('builds from total and query option', function (): void {
    $options = (new QueryOption)->setPage(2)->setPerPage(10);

    $meta = MetaPagination::fromTotalAndQueryOption(35, $options);

    expect($meta->page)->toBe(2);
    expect($meta->perPage)->toBe(10);
    expect($meta->total)->toBe(35);
    expect($meta->pageCount)->toBe(4);
});

it('builds from length aware paginator', function (): void {
    $items = new Collection([['id' => 1]]);
    $paginator = new LengthAwarePaginator($items, 30, 10, 2);

    $meta = MetaPagination::fromLengthAwarePaginator($paginator);

    expect($meta->page)->toBe(2);
    expect($meta->perPage)->toBe(10);
    expect($meta->total)->toBe(30);
    expect($meta->pageCount)->toBe(3);
});

it('returns snake case and camel case arrays', function (): void {
    $meta = new MetaPagination(1, 15, 20, 2);

    expect($meta->toArray())->toEqual([
        'page' => 1,
        'per_page' => 15,
        'total' => 20,
        'page_count' => 2,
    ]);

    expect($meta->toArray(false))->toEqual([
        'page' => 1,
        'perPage' => 15,
        'total' => 20,
        'pageCount' => 2,
    ]);
});
