<?php

namespace App\Repositories\StarTransaction;

use App\Models\StarTransaction;
use App\Repositories\Repository;

class StarTransactionRepository extends Repository implements IStarTransactionRepository
{
    public function __construct(StarTransaction $model)
    {
        parent::__construct($model);
    }
}
