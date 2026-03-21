<?php

namespace App\Repositories\Category;

use App\Repositories\IRepository;

interface ICategoryRepository extends IRepository
{
    /**
     * @return array<int, array{id:int,name:string,slug:?string,course_count:int}>
     */
    public function getCourseFilterCategories(): array;
}
