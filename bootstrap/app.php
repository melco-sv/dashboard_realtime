<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 1. Alias Middleware kamu yang lama (JANGAN DIHAPUS)
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // 2. TAMBAHAN BARU: Matikan CSRF Token khusus untuk route /ai-report
        $middleware->validateCsrfTokens(except: [
            '/ai-report',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
