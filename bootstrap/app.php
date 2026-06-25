<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // MENYUNTIKKAN HEADER CORS SECARA PAKSA SECARA GLOBAL
        $middleware->append(function (Request $request, $next) {
            // Jika browser mengirim Preflight request (OPTIONS), langsung jawab sukses dengan header lengkap
            if ($request->getMethod() === "OPTIONS") {
                return response('', 200)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin');
            }

            // Untuk request biasa (POST/GET), jalankan lalu tambahkan header CORS di akhir respon
            $response = $next($request);
            
            // Cek apakah response berupa objek respon Laravel biasa (bukan file biner, dll)
            if (method_exists($response, 'header')) {
                $response->header('Access-Control-Allow-Origin', '*')
                         ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                         ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin');
            }

            return $response;
        });

        // Matikan proteksi CSRF khusus untuk semua jalur API
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Cegah redirect otomatis 302 ke halaman login jika terjadi error di API
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi habis atau tidak valid.'
                ], 401);
            }
        });
    })->create();