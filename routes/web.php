<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EstadoController;

use App\Http\Controllers\Contractual\DocumentoRevisionContractualController;
use App\Http\Controllers\Contractual\RevisionContractualController;
use App\Http\Controllers\Contractual\SnapshotRevisionContractualController;
use App\Http\Controllers\Contractual\AnalisisRevisionContractualController;



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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});



// ruta web ESTADOS
Route::middleware(['auth'])->group(function () {
    Route::get('/estados', [EstadoController::class, 'index'])
        ->name('estados.index');
});

// ruta API Estados
Route::middleware('auth:sanctum')->get('/estados', [EstadoController::class, 'index']);


Route::middleware(['auth'])
    ->prefix('contractual')
    ->name('contractual.')
    ->group(function () {

        Route::resource('revisiones', RevisionContractualController::class)
            ->parameters([
                'revisiones' => 'revision',
            ]);

        Route::get('revisiones/{revision}/documentos', [DocumentoRevisionContractualController::class, 'index'])
            ->name('revisiones.documentos.index');

        Route::post('revisiones/{revision}/documentos', [DocumentoRevisionContractualController::class, 'store'])
            ->name('revisiones.documentos.store');

        Route::delete('revisiones/{revision}/documentos/{documento}', [DocumentoRevisionContractualController::class, 'destroy'])
            ->name('revisiones.documentos.destroy');

    // rutas snapshots
        Route::get('revisiones/{revision}/snapshots', [SnapshotRevisionContractualController::class, 'index'])
            ->name('revisiones.snapshots.index');

        Route::post('revisiones/{revision}/snapshots', [SnapshotRevisionContractualController::class, 'store'])
            ->name('revisiones.snapshots.store');

        Route::get('revisiones/{revision}/snapshots/{snapshot}', [SnapshotRevisionContractualController::class, 'show'])
            ->name('revisiones.snapshots.show');

          

        Route::post('revisiones/{revision}/analizar', [AnalisisRevisionContractualController::class, 'store'])->name('revisiones.analizar');


    });




require __DIR__.'/auth.php';




