<?php

use App\Http\Controllers\Presupuesto\ExpedienteController;
use App\Http\Controllers\Presupuesto\ExpedienteWorkflowController;
use Illuminate\Support\Facades\Route;

Route::prefix('presupuesto')
    ->name('presupuesto.')
    ->middleware(['auth'])
    ->group(function () {
        Route::resource('expedientes', ExpedienteController::class)
            ->only(['index', 'create', 'store', 'show']);

        Route::patch('expedientes/{expediente}/workflow', [ExpedienteWorkflowController::class, 'update'])
            ->name('expedientes.workflow.update');
    });
