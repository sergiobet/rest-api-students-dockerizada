<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Le indicamos a Laravel que confíe en los proxies
        $middleware->trustProxies(at: '*'); //* El '*' significa que confiamos en cualquier proxy que nos envíe Render
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Capturamos el error de "No encontrado" (404)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            
            // Si la petición pide JSON (como en una API)
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 404,
                    'message' => 'El recurso solicitado no existe o no fue encontrado.'
                ], 404);
            }
        });
        
    })->create();
