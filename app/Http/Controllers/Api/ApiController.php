<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Jsonable;
abstract class ApiController extends Controller {
    use Jsonable;
}