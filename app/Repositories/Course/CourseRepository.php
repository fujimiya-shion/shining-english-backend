<?php
namespace App\Repositories\Course;

use App\Models\Course;
use App\Repositories\Repository;
use App\ValueObjects\CourseFilter;
use App\ValueObjects\QueryOption;
use Illuminate\Pagination\LengthAwarePaginator;
class CourseRepository extends Repository implements ICourseRepository {
    public function __construct(Course $model) {
        $this->model = $model;
    }

    public function filter(CourseFilter $filters): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if ($filters->categoryId !== null) {
            $query->where('category_id', $filters->categoryId);
        }

        if ($filters->status !== null) {
            $query->where('status', $filters->status);
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

}
