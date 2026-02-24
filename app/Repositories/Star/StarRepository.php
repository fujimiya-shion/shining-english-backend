<?php

namespace App\Repositories\Star;

use App\Models\Star;
use App\Repositories\Repository;

class StarRepository extends Repository implements IStarRepository
{
    public function __construct(Star $model)
    {
        parent::__construct($model);
    }
}
