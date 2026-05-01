<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function ($response, Throwable $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'Error',
                    'message' => $e->getMessage(),
                    'data' => null,
                ], $response->getStatusCode() ?: 500);
            }

            return $response;
        });
    })->create();
