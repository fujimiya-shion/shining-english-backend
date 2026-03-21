<?php

namespace App\Http\Middleware;

use App\Traits\Jsonable;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class VerifyDeveloperToken
{
    use Jsonable;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        $developer = PersonalAccessToken::where([
            'name' => 'developer_access_token',
            'token' => $token,
        ])->first();
        if(! $developer)
            return $this->unauthorized("Access Token is not set or invalid");
        return $next($request);
    }
}
