<?php

namespace App\Http\Controllers\Presupuesto;

use App\Http\Controllers\Controller;
use App\Models\Estado;
use App\Models\Funcionario;
use App\Models\Presupuesto\ExpedientePresupuestario;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExpedienteController extends Controller
{
    public function index()
    {
        $expedientes = ExpedientePresupuestario::with(['estado', 'solicitante', 'responsable'])
            ->latest()
            ->paginate(15);

        return view('presupuesto.expedientes.index', compact('expedientes'));
    }

    public function create()
    {
        return view('presupuesto.expedientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'solicitante_rut' => ['required', 'exists:funcionarios,rut'],
            'responsable_rut' => ['nullable', 'exists:funcionarios,rut'],
            'ccosto' => ['required', 'exists:ccostos,ccosto'],
            'cfinanciero' => ['nullable', 'exists:cfinancieros,cfinanciero'],
            'cuenta_presupuestaria' => ['required', 'string', 'max:20'],
            'denominacion' => ['nullable', 'string', 'max:255'],
            'monto' => ['required', 'numeric', 'min:0'],
            'moneda' => ['nullable', 'string', 'max:10'],
            'glosa' => ['required', 'string'],
            'caracter_gasto' => ['nullable', 'string', 'max:50'],
            'medio_solicitud' => ['nullable', 'string', 'max:100'],
            'numero_requerimiento' => ['nullable', 'string', 'max:50'],
        ]);

        $estadoInicial = Estado::query()
            ->where('nombre', 'Ingresado')
            ->where('tabla_referencia', 'expedientes_presupuestarios')
            ->firstOrFail();

        $anio = (int) now()->format('Y');
        $siguiente = ExpedientePresupuestario::query()
            ->where('anio', $anio)
            ->count() + 1;

        $correlativo = sprintf('CDP-%s-%03d', $anio, $siguiente);

        $expediente = ExpedientePresupuestario::create([
            'correlativo' => $correlativo,
            'anio' => $anio,
            'solicitante_rut' => $request->solicitante_rut,
            'responsable_rut' => $request->responsable_rut,
            'ccosto' => $request->ccosto,
            'cfinanciero' => $request->cfinanciero,
            'cuenta_presupuestaria' => $request->cuenta_presupuestaria,
            'denominacion' => $request->denominacion,
            'monto' => $request->monto,
            'moneda' => $request->moneda ?? 'CLP',
            'total_moneda_compra' => $request->monto,
            'glosa' => $request->glosa,
            'caracter_gasto' => $request->caracter_gasto,
            'medio_solicitud' => $request->medio_solicitud,
            'numero_requerimiento' => $request->numero_requerimiento,
            'estado_id' => $estadoInicial->id,
        ]);

        return redirect()->route('presupuesto.expedientes.show', $expediente)
            ->with('success', 'Expediente creado correctamente.');
    }

    public function show(ExpedientePresupuestario $expediente)
    {
        $expediente->load([
            'estado',
            'solicitante',
            'responsable',
            'historial.estado',
            'historial.usuario',
            'tareas',
            'observaciones',
            'adjuntos',
        ]);

        return view('presupuesto.expedientes.show', compact('expediente'));
    }
}
