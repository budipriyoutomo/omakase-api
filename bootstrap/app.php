<?php

use App\Shared\Exceptions\ApiException;
use App\Shared\Http\Middleware\JwtMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: '',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register JWT middleware alias
        $middleware->alias([
            'jwt' => JwtMiddleware::class,
        ]);

        // CORS — allow Next.js frontend origin
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Validation errors → 422 JSON
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('v1/*')) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'status'  => 422,
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // Custom ApiException → structured JSON
        $exceptions->render(function (ApiException $e, Request $request) {
            if ($request->expectsJson() || $request->is('v1/*')) {
                return $e->render();
            }
        });

        // 404 → JSON
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('v1/*')) {
                return response()->json([
                    'message' => 'Resource not found.',
                    'status'  => 404,
                ], 404);
            }
        });

        // 405 → JSON
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('v1/*')) {
                return response()->json([
                    'message' => 'Method not allowed.',
                    'status'  => 405,
                ], 405);
            }
        });

    })->create();
