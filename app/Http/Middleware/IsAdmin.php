<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userAuth = auth('api')->user();
        if ($userAuth && $userAuth->role === 'admin') {
            return $next($request);
        } else {
            return response()->json(['error' => 'No admin user'], Response::HTTP_UNAUTHORIZED);
        }


    }
}
