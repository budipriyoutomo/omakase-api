<?php

declare(strict_types=1);

namespace App\Shared\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'status'  => 401,
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'Token is invalid or expired.',
                'status'  => 401,
            ], 401);
        }

        return $next($request);
    }
}
