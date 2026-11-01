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
        // Pengecualian CSRF untuk logout agar tidak terjadi 419 Page Expired
        $middleware->validateCsrfTokens(except: [
            'logout',
        ]);

        // Global middleware untuk semua request web
        $middleware->append(\App\Http\Middleware\CheckActiveAccount::class);

        // Named middleware (alias)
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Tangani 419 Page Expired secara anggun dan arahkan ke login dengan pesan yang ramah
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->is('logout')) {
                return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
            }
            return redirect()->route('login')->with('warning', 'Sesi Anda telah berakhir. Silakan masuk kembali.');
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->is('logout')) {
                    return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
                }
                return redirect()->route('login')->with('warning', 'Sesi Anda telah berakhir. Silakan masuk kembali.');
            }
        });
    })->create();
