<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Services\User\IUserService;
use Illuminate\Http\Request;

class AuthController extends ApiController {
    public function __construct(
        private IUserService $service
    ) {}

    
}
