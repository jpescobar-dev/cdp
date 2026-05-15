<?php

use App\Http\Controllers\MesaAyuda\CdpBorradorController;
use App\Http\Controllers\MesaAyuda\MesaAyudaExtraccionController;
use App\Http\Controllers\MesaAyuda\MesaAyudaRequerimientoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])
    ->prefix('mesa-ayuda')
    ->name('mesa-ayuda.')
    ->group(function () {
        Route::get('/extracciones', [MesaAyudaExtraccionController::class, 'index'])
            ->name('extracciones.index');

        Route::post('/extracciones/ejecutar', [MesaAyudaExtraccionController::class, 'ejecutar'])
            ->name('extracciones.ejecutar');

        Route::get('/requerimientos', [MesaAyudaRequerimientoController::class, 'index'])
            ->name('requerimientos.index');

        Route::get('/requerimientos/{requerimiento}', [MesaAyudaRequerimientoController::class, 'show'])
            ->name('requerimientos.show');

        Route::get('/adjuntos/{adjunto}/ver', [MesaAyudaRequerimientoController::class, 'verAdjunto'])
            ->name('adjuntos.ver');

        Route::post('/requerimientos/{requerimiento}/reclasificar', [MesaAyudaRequerimientoController::class, 'reclasificar'])
            ->name('requerimientos.reclasificar');

        Route::post('/requerimientos/{requerimiento}/crear-expediente', [MesaAyudaRequerimientoController::class, 'crearExpediente'])
            ->name('requerimientos.crear-expediente');

        Route::post('/requerimientos/{requerimiento}/generar-cdp-borrador', [CdpBorradorController::class, 'generarDesdeRequerimiento'])
            ->name('requerimientos.generar-cdp-borrador');

        Route::get('/api/proximo-numero-cdp', [CdpBorradorController::class, 'apiProximoNumeroCdp'])
            ->name('api.proximo-numero-cdp');

        Route::get('/api/paridad-uf', [CdpBorradorController::class, 'apiParidadUf'])
            ->name('api.paridad-uf');

        Route::get('/cdp-borradores', [CdpBorradorController::class, 'index'])
            ->name('cdp-borradores.index');

        Route::get('/cdp-borradores/{borrador}', [CdpBorradorController::class, 'show'])
            ->name('cdp-borradores.show');

        Route::patch('/cdp-borradores/{borrador}', [CdpBorradorController::class, 'update'])
            ->name('cdp-borradores.update');

        Route::post('/cdp-borradores/{borrador}/aprobar', [CdpBorradorController::class, 'aprobar'])
            ->name('cdp-borradores.aprobar');

        Route::post('/cdp-borradores/{borrador}/rechazar', [CdpBorradorController::class, 'rechazar'])
            ->name('cdp-borradores.rechazar');

        Route::post('/cdp-borradores/{borrador}/observar', [CdpBorradorController::class, 'observar'])
            ->name('cdp-borradores.observar');
    });
