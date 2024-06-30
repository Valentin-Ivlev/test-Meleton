<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token || !$this->validateToken($token)) {
            return response()->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Invalid token'
            ], 403);
        }

        return $next($request);
    }

    private function validateToken($token)
    {
        // Для примера используем фиксированный токен
        $validToken = 'abcABC12345-67890_abcdefghijklmnop-qrstuvwxyz_ABCDEFGHIJKLMNOPQ';
        return $token === $validToken;
    }
}
