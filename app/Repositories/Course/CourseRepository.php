<?php

namespace App\Repositories\Course;

use App\Models\Category;
use App\Models\Course;
use App\Models\Level;
use App\Repositories\Repository;
use App\ValueObjects\CourseFilter;
use App\ValueObjects\QueryOption;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseRepository extends Repository implements ICourseRepository
{
    public function __construct(Course $model)
    {
        $this->model = $model;
    }

    public function filter(CourseFilter $filters): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->active();

        if ($filters->categoryId !== null) {
            $query->where('category_id', $filters->categoryId);
        }

        if ($filters->levelId !== null) {
            $query->where('level_id', $filters->levelId);
        }

        if ($filters->priceMin !== null && $filters->priceMax !== null) {
            $query->whereBetween('price', [$filters->priceMin, $filters->priceMax]);
        } elseif ($filters->priceMin !== null) {
            $query->where('price', '>=', $filters->priceMin);
        } elseif ($filters->priceMax !== null) {
            $query->where('price', '<=', $filters->priceMax);
        }

        if ($filters->ratingMin !== null && $filters->ratingMax !== null) {
            $query->whereBetween('rating', [$filters->ratingMin, $filters->ratingMax]);
        } elseif ($filters->ratingMin !== null) {
            $query->where('rating', '>=', $filters->ratingMin);
        } elseif ($filters->ratingMax !== null) {
            $query->where('rating', '<=', $filters->ratingMax);
        }

        if ($filters->learnedMin !== null && $filters->learnedMax !== null) {
            $query->whereBetween('learned', [$filters->learnedMin, $filters->learnedMax]);
        } elseif ($filters->learnedMin !== null) {
            $query->where('learned', '>=', $filters->learnedMin);
        } elseif ($filters->learnedMax !== null) {
            $query->where('learned', '<=', $filters->learnedMax);
        }

        if ($filters->keyword !== null) {
            $query->whereRaw('MATCH(name, slug) AGAINST (? IN BOOLEAN MODE)', [$filters->keyword]);
        }

        $options = $filters->options ?? new QueryOption;
        $query = $this->applyQueryOption($query, $options);

        return $query->paginate(perPage: $options->perPage, page: $options->page);
    }

    public function getFilterProps(): array
    {
        $categories = Category::query()
            ->whereHas('courses', fn ($query) => $query->active())
            ->withCount([
                'courses as courses_count' => fn ($query) => $query->active(),
            ])
            ->orderBy('name')
            ->get(['id', 'name', 'slug'])
            ->map(fn (Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'course_count' => $category->courses_count ?? 0,
            ])
            ->values()
            ->all();

        $range = $this->model->newQuery()
            ->active()
            ->selectRaw('MIN(price) as price_min')
            ->selectRaw('MAX(price) as price_max')
            ->selectRaw('MIN(rating) as rating_min')
            ->selectRaw('MAX(rating) as rating_max')
            ->selectRaw('MIN(learned) as learned_min')
            ->selectRaw('MAX(learned) as learned_max')
            ->first();

        $levels = Level::query()
            ->whereHas('courses', fn ($query) => $query->active())
            ->withCount([
                'courses as courses_count' => fn ($query) => $query->active(),
            ])
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Level $level): array => [
                'value' => $level->id,
                'label' => $level->name,
                'count' => $level->courses_count ?? 0,
            ])
            ->values()
            ->all();

        return [
            'categories' => $categories,
            'price' => [
                'min' => $range?->price_min !== null ? (int) $range->price_min : null,
                'max' => $range?->price_max !== null ? (int) $range->price_max : null,
            ],
            'rating' => [
                'min' => $range?->rating_min !== null ? (float) $range->rating_min : null,
                'max' => $range?->rating_max !== null ? (float) $range->rating_max : null,
            ],
            'learned' => [
                'min' => $range?->learned_min !== null ? (int) $range->learned_min : null,
                'max' => $range?->learned_max !== null ? (int) $range->learned_max : null,
            ],
            'levels' => $levels,
        ];
    }
}
