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
        // Percayakan proxy load balancer (Penting untuk Railway/Cloudflare)
        $middleware->trustProxies(at: '*');

        // Daftarkan alias middleware untuk role-based access control
        $middleware->alias([
            'admin'     => \App\Http\Middleware\AdminMiddleware::class,
            'pelanggan' => \App\Http\Middleware\PelangganMiddleware::class,
            'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class,
        ]);

        // Exclude Webhook dari CSRF
        $middleware->validateCsrfTokens(except: [
            'webhook/midtrans',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            // Hanya aktifkan persembunyian stack trace jika debug mode mati (Production)
            if (!config('app.debug')) {
                // Untuk API call / AJAX request, kembalikan format JSON aman
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Terjadi kesalahan sistem internal. Silakan hubungi admin.'
                    ], 500);
                }

                // Untuk HTTP biasa, arahkan ke error page 500 buatan sendiri
                return response()->view('errors.500', [
                    'exception' => $e
                ], 500);
            }
        });
    })->create();
