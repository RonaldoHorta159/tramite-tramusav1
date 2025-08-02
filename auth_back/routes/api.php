<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\OficinaController;
use App\Http\Controllers\DocumentoController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::middleware('auth:api')->group(function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('seguimientos/por-recibir', [SeguimientoController::class, 'porRecibir']);
            // Seguimiento de documentos
            Route::patch('seguimientos/{seguimiento}/recibir', [SeguimientoController::class, 'recibir']);

            Route::resource('seguimientos', SeguimientoController::class);
            Route::post('seguimientos/{seguimiento}/derivar', [SeguimientoController::class, 'derivar']);
            Route::patch('seguimientos/{seguimiento}/anular', [SeguimientoController::class, 'anular']);
            Route::patch('seguimientos/{seguimiento}/observar', [SeguimientoController::class, 'observar']);

            Route::resource('oficinas', OficinaController::class); // <-- AÑADE ESTA LÍNEA
            Route::get('documentos/{documento}/historial', [DocumentoController::class, 'historial']);
        });

    });
});

Route::get('/', [AuthController::class, 'unauthorized'])->name('login');
