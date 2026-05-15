<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EstadoController;

use App\Http\Controllers\Presupuesto\ExpedienteWorkflowController;
use App\Http\Controllers\Presupuesto\ExpedienteController;



Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/usuarios', [UserController::class, 'index'])
        ->middleware('permission:ver usuarios')
        ->name('usuarios.index');

    Route::get('/usuarios/create', [UserController::class, 'create'])
        ->middleware('permission:crear usuarios')
        ->name('usuarios.create');

    Route::post('/usuarios', [UserController::class, 'store'])
        ->middleware('permission:crear usuarios')
        ->name('usuarios.store');

    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:eliminar usuarios')
        ->name('usuarios.destroy');
});

Route::middleware(['auth', 'permission:ver dashboard'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});



// ruta web ESTADOS
Route::middleware(['auth'])->group(function () {
    Route::get('/estados', [EstadoController::class, 'index'])
        ->name('estados.index');
});

// ruta API Estados
Route::middleware('auth:sanctum')->get('/estados', [EstadoController::class, 'index']);



Route::prefix('presupuesto')->name('presupuesto.')->group(function () {
    Route::post(
        'expedientes/{expediente}/cambiar-estado',
        [ExpedienteWorkflowController::class, 'cambiarEstado']
    )->name('expedientes.cambiar-estado');
});



Route::prefix('presupuesto')
    ->name('presupuesto.')
    ->middleware(['auth'])
    ->group(function () {

        Route::resource('expedientes', ExpedienteController::class);

        Route::post(
            'expedientes/{expediente}/cambiar-estado',
            [ExpedienteWorkflowController::class, 'cambiarEstado']
        )->name('expedientes.cambiar-estado');
    });

require __DIR__.'/cdp.php';

require __DIR__.'/mesa_ayuda.php';

require __DIR__.'/auth.php';



