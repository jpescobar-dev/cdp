<?php

namespace App\Http\Controllers\MesaAyuda;

use App\Http\Controllers\Controller;
use App\Jobs\EjecutarExtraccionMesaAyudaJob;
use App\Models\MesaAyudaExtraccion;
use App\Services\MesaAyuda\EjecutarExtraccionMesaAyudaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MesaAyudaExtraccionController extends Controller
{
    public function index()
    {
        $extracciones = collect();

        if (class_exists(MesaAyudaExtraccion::class) && Schema::hasTable('mesa_ayuda_extracciones')) {
            $extracciones = MesaAyudaExtraccion::query()
                ->latest()
                ->limit(25)
                ->get();
        }

        return view('mesa-ayuda.extracciones.index', compact('extracciones'));
    }

    public function ejecutar(Request $request, EjecutarExtraccionMesaAyudaService $service)
    {
        $request->validate([
            'modo' => ['nullable', 'in:sincrono,job'],
            'max_folios' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $usuarioRut = optional($request->user())->rut;

        if ($request->input('modo') === 'job' && class_exists(EjecutarExtraccionMesaAyudaJob::class)) {
            EjecutarExtraccionMesaAyudaJob::dispatch($usuarioRut, [
                'max_folios' => $request->integer('max_folios', (int) config('mesa_ayuda.max_folios', 0)),
            ]);

            return redirect()
                ->route('mesa-ayuda.extracciones.index')
                ->with('success', 'La extracción fue enviada a la cola de trabajos.');
        }

        try {
            $resultado = $service->ejecutar($usuarioRut, [
                'max_folios' => $request->integer('max_folios', (int) config('mesa_ayuda.max_folios', 0)),
            ]);

            return redirect()
                ->route('mesa-ayuda.extracciones.index')
                ->with('success', 'Extracción ejecutada correctamente. JSON: '.($resultado['ruta_json'] ?? 'sin ruta'));
        } catch (Throwable $e) {
            return redirect()
                ->route('mesa-ayuda.extracciones.index')
                ->with('error', 'Error ejecutando extracción: '.$e->getMessage());
        }
    }
}
