<?php

namespace App\Repositories\Course;

use App\Models\Course;
use App\Models\Level;
use App\Repositories\Category\ICategoryRepository;
use App\Repositories\Repository;
use App\ValueObjects\CourseFilter;
use App\ValueObjects\QueryOption;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseRepository extends Repository implements ICourseRepository
{
    public function __construct(
        Course $model,
        protected ICategoryRepository $categoryRepository
    ) {
        $this->model = $model;
    }

    public function getBySlug(string $slug): ?Course
    {
        return $this->model->newQuery()
            ->with([
                'category:id,name,slug',
                'level:id,name',
                'reviews' => fn ($query) => $query
                    ->select(['id', 'course_id', 'user_id', 'rating', 'content', 'created_at'])
                    ->with(['user:id,name,avatar'])
                    ->orderByDesc('created_at'),
                'lessons' => fn ($query) => $query
                    ->select([
                        'id',
                        'name',
                        'slug',
                        'course_id',
                        'lesson_group_id',
                        'group_name',
                        'group_order',
                        'lesson_order',
                        'video_url',
                        'documents',
                        'document_names',
                        'description',
                        'duration_minutes',
                        'has_quiz',
                        'star_reward_video',
                        'star_reward_quiz',
                    ])
                    ->with([
                        'comments' => fn ($commentQuery) => $commentQuery
                            ->select(['id', 'lesson_id', 'user_id', 'content', 'created_at'])
                            ->with(['user:id,name,avatar'])
                            ->orderByDesc('created_at'),
                    ])
                    ->orderBy('group_order')
                    ->orderBy('lesson_order')
                    ->orderBy('id'),
            ])
            ->where('status', true)
            ->where('slug', $slug)
            ->first();
    }

    public function filter(CourseFilter $filters): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->where('status', true);

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
            $keyword = trim($filters->keyword);
            $booleanKeyword = collect(preg_split('/\s+/', $keyword))
                ->filter()
                ->map(fn (string $term): string => "{$term}*")
                ->implode(' ');

            $query->where(function ($query) use ($booleanKeyword, $keyword): void {
                $query->whereRaw('MATCH(name, slug) AGAINST (? IN BOOLEAN MODE)', [$booleanKeyword])
                    ->orWhere('name', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%");
            });
        }

        $options = $filters->options ?? new QueryOption;
        $query = $this->applyQueryOption($query, $options);

        return $query->paginate(perPage: $options->perPage, page: $options->page);
    }

    public function getFilterProps(): array
    {
        $categories = $this->categoryRepository->getCourseFilterCategories();

        $range = $this->model->newQuery()
            ->where('status', true)
            ->selectRaw('MIN(price) as price_min')
            ->selectRaw('MAX(price) as price_max')
            ->selectRaw('MIN(rating) as rating_min')
            ->selectRaw('MAX(rating) as rating_max')
            ->selectRaw('MIN(learned) as learned_min')
            ->selectRaw('MAX(learned) as learned_max')
            ->first();

        $levels = Level::query()
            ->whereHas('courses', fn ($query) => $query->where('status', true))
            ->withCount([
                'courses as courses_count' => fn ($query) => $query->where('status', true),
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
