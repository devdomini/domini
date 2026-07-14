<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'backoffice' => \App\Http\Middleware\EnsureBackOfficeUser::class,
            'admin.only' => \App\Http\Middleware\EnsureAdminRole::class,
        ]);

        // API messagerie web (session auth + backoffice) : évite les 419 AJAX.
        $middleware->validateCsrfTokens(except: [
            'admin/messenger-api',
            'admin/messenger-api/*',
            'admin/messenger-api/*/*',
            'admin/messenger-api/*/*/*',
        ]);

        $middleware->redirectGuestsTo('/admin/login');
        
        // Configuration CORS pour l'application mobile
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expirée. Rechargez la page et réessayez.',
                ], 419);
            }
        });
    })->create();
