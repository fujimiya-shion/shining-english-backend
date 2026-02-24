<?php

namespace App\Services\StarTransaction;

use App\Repositories\StarTransaction\IStarTransactionRepository;
use App\Services\Service;

class StarTransactionService extends Service implements IStarTransactionService
{
    public function __construct(IStarTransactionRepository $repository)
    {
        parent::__construct($repository);
    }
}
