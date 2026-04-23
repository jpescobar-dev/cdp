<?php

namespace App\Http\Controllers\Contractual;

use \Log;
use App\Http\Controllers\Controller;
use App\Models\RevisionContractual;
use App\Services\Contractual\OpenAIRevisionContractualService;

class AnalisisRevisionContractualController extends Controller
{
    // public function store(RevisionContractual $revision, OpenAIRevisionContractualService $service)
    // {
    //     $data = $service->analizar($revision);

    //     return back()->with('success', 'Análisis ejecutado (debug)');
    // }

    public function store(RevisionContractual $revision, OpenAIRevisionContractualService $service)
    {
        Log::info('Entró al análisis', ['revision_id' => $revision->id]);

        $data = $service->analizar($revision);

        \Log::info('Respuesta análisis', ['data' => $data]);

        return back()->with('success', 'Análisis ejecutado (debug)');
    }
}
