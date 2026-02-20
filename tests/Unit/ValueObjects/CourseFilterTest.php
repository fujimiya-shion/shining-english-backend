<?php

use App\ValueObjects\CourseFilter;
use Tests\TestCase;

uses(TestCase::class);

it('builds course filter from array', function (): void {
    $filters = CourseFilter::fromArray([
        'category_id' => 3,
        'status' => 'true',
        'price_min' => 100,
        'price_max' => 300,
        'rating_min' => 3.5,
        'rating_max' => 4.5,
        'learned_min' => 10,
        'learned_max' => 20,
        'q' => '  basic  ',
        'page' => 2,
        'perPage' => 5,
    ]);

    expect($filters->categoryId)->toBe(3);
    expect($filters->status)->toBeTrue();
    expect($filters->priceMin)->toBe(100);
    expect($filters->priceMax)->toBe(300);
    expect($filters->ratingMin)->toBe(3.5);
    expect($filters->ratingMax)->toBe(4.5);
    expect($filters->learnedMin)->toBe(10);
    expect($filters->learnedMax)->toBe(20);
    expect($filters->keyword)->toBe('basic');
    expect($filters->options?->getPage())->toBe(2);
    expect($filters->options?->getPerPage())->toBe(5);
});

it('sets keyword to null when empty', function (): void {
    $filters = CourseFilter::fromArray([
        'q' => '   ',
    ]);

    expect($filters->keyword)->toBeNull();
});

it('falls back to name when q is empty', function (): void {
    $filters = CourseFilter::fromArray([
        'q' => '   ',
        'name' => '  english  ',
    ]);

    expect($filters->keyword)->toBe('english');
});

it('keeps status null when invalid', function (): void {
    $filters = CourseFilter::fromArray([
        'status' => 'invalid',
        'q' => null,
    ]);

    expect($filters->status)->toBeNull();
    expect($filters->keyword)->toBeNull();
});

it('sets keyword null when name is empty and q missing', function (): void {
    $filters = CourseFilter::fromArray([
        'name' => '   ',
    ]);

    expect($filters->keyword)->toBeNull();
});
