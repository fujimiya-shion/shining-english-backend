<?php

namespace App\Filament\Resources;

use App\Services\IService;
use Filament\Resources\Resource;

abstract class BaseResource extends Resource
{
    abstract protected function service(): IService;
}
