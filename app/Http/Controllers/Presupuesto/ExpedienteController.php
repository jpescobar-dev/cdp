<?php

namespace App\Http\Controllers\Presupuesto;

use App\Http\Controllers\Controller;
use App\Models\Ccosto;
use App\Models\Cfinanciero;
use App\Models\Estado;
use App\Models\Funcionario;
use App\Models\Presupuesto\ExpedienteHistorial;
use App\Models\Presupuesto\ExpedientePresupuestario;
use Illuminate\Http\Request;

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
        $ccostos = Ccosto::orderBy('nombre')->get();
        $cfinancieros = Cfinanciero::orderBy('nombre')->get();
        $funcionarios = Funcionario::where('activo', true)->orderBy('nombre_completo')->get();

        return view('presupuesto.expedientes.create', compact('ccostos', 'cfinancieros', 'funcionarios'));
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
            'monto' => ['required', 'numeric', 'min:1'],
            'glosa' => ['required', 'string'],
        ]);

        $estadoInicial = Estado::where('nombre', 'Ingresado')
            ->where('tabla_referencia', 'expedientes_presupuestarios')
            ->firstOrFail();

        $anio = now()->year;
        $correlativo = 'CDP-' . $anio . '-' . str_pad((string) (ExpedientePresupuestario::where('anio', $anio)->count() + 1), 3, '0', STR_PAD_LEFT);

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
            'total_moneda_compra' => $request->total_moneda_compra ?? $request->monto,
            'glosa' => $request->glosa,
            'caracter_gasto' => $request->caracter_gasto,
            'medio_solicitud' => $request->medio_solicitud,
            'numero_requerimiento' => $request->numero_requerimiento,
            'estado_id' => $estadoInicial->id,
        ]);

        $funcionario = Funcionario::where('email', auth()->user()->email)->first();

        if ($funcionario) {
            ExpedienteHistorial::create([
                'expediente_id' => $expediente->id,
                'estado_id' => $estadoInicial->id,
                'usuario_rut' => $funcionario->rut,
                'comentario' => 'Expediente creado.',
                'fecha_cambio' => now(),
            ]);
        }

        return redirect()->route('presupuesto.expedientes.show', $expediente)
            ->with('success', 'Expediente creado correctamente.');
    }

    public function show(ExpedientePresupuestario $expediente)
    {
        $expediente->load(['estado', 'solicitante', 'responsable', 'historial.estado', 'historial.usuario', 'tareas']);

        return view('presupuesto.expedientes.show', compact('expediente'));
    }
}
