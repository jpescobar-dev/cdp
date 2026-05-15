<?php

use App\Http\Controllers\Cdp\CdpSolicitudController;
use Illuminate\Support\Facades\Route;

Route::prefix('cdp')
    ->name('cdp.')
    ->middleware(['auth'])
    ->group(function () {

        Route::resource('solicitudes', CdpSolicitudController::class)
            ->only(['index', 'create', 'store', 'show'])
            ->parameters(['solicitudes' => 'solicitud']);

        Route::get('solicitudes/{solicitud}/descargar', [CdpSolicitudController::class, 'descargar'])
            ->name('solicitudes.descargar');
    });
