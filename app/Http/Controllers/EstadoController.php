<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tablaReferencia = $request->get('tabla_referencia');

        $query = Estado::query()->orderBy('nombre');

        if (!empty($tablaReferencia)) {
            $query->where('tabla_referencia', $tablaReferencia);
        }

        return response()->json($query->get());
    }
}