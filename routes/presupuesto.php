<?php

use App\Http\Controllers\Presupuesto\ExpedienteController;
use App\Http\Controllers\Presupuesto\ExpedienteWorkflowController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('presupuesto')->name('presupuesto.')->group(function () {
    Route::resource('expedientes', ExpedienteController::class)->only(['index', 'create', 'store', 'show']);

    Route::post('expedientes/{expediente}/cambiar-estado', [ExpedienteWorkflowController::class, 'cambiarEstado'])
        ->name('expedientes.cambiar-estado');
});
