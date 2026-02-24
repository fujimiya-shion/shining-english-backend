<?php

namespace App\Services\Star;

use App\Repositories\Star\IStarRepository;
use App\Services\Service;

class StarService extends Service implements IStarService
{
    public function __construct(IStarRepository $repository)
    {
        parent::__construct($repository);
    }
}
