<?php

namespace App\Services\Paridad;

use App\Models\ParidadUf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ObtenerUfCmfService
{
    /**
     * Obtiene el valor UF para una fecha dada.
     * Intenta primero la API de CMF; si falla, usa mindicador.cl como respaldo.
     * Persiste el valor en paridad_ufs para consultas futuras.
     *
     * @return array{valor: float, fuente: string}|null
     */
    public function obtenerYGuardar(string $fecha): ?array
    {
        // 1) Intentar API de CMF (requiere CMF_API_KEY en .env)
        $resultadoCmf = $this->consultarCmf($fecha);
        if ($resultadoCmf !== null) {
            $this->persistir($fecha, $resultadoCmf);
            return ['valor' => $resultadoCmf, 'fuente' => 'CMF'];
        }

        // 2) Fallback: mindicador.cl (público, sin clave)
        $resultadoMindicador = $this->consultarMindicador($fecha);
        if ($resultadoMindicador !== null) {
            $this->persistir($fecha, $resultadoMindicador);
            return ['valor' => $resultadoMindicador, 'fuente' => 'mindicador.cl'];
        }

        return null;
    }

    private function consultarCmf(string $fecha): ?float
    {
        $apiKey = config('services.cmf.api_key');
        if (!$apiKey) {
            return null;
        }

        [$year, $month, $day] = explode('-', $fecha);

        try {
            $response = Http::timeout(10)->get(
                "https://api.cmfchile.cl/api-sbifv3/recursos/uf/{$year}/{$month}/dias/{$day}",
                ['apikey' => $apiKey, 'formato' => 'json']
            );

            if (!$response->successful()) {
                Log::debug("CMF API respondió HTTP {$response->status()} para UF {$fecha}.");
                return null;
            }

            $ufItems = $response->json('UFs', []);
            if (empty($ufItems)) {
                return null;
            }

            // La API retorna valor en formato chileno: "37.262,17"
            $valorStr = $ufItems[0]['Valor'] ?? null;
            if ($valorStr === null) {
                return null;
            }

            return (float) str_replace(['.', ','], ['', '.'], $valorStr);

        } catch (\Throwable $e) {
            Log::debug("Error consultando CMF API para UF ({$fecha}): " . $e->getMessage());
            return null;
        }
    }

    private function consultarMindicador(string $fecha): ?float
    {
        // mindicador.cl espera la fecha en formato DD-MM-YYYY
        $fechaFormateada = implode('-', array_reverse(explode('-', $fecha)));

        try {
            $response = Http::timeout(10)->get(
                "https://mindicador.cl/api/uf/{$fechaFormateada}"
            );

            if (!$response->successful()) {
                Log::debug("mindicador.cl respondió HTTP {$response->status()} para UF {$fecha}.");
                return null;
            }

            $serie = $response->json('serie', []);
            if (empty($serie)) {
                return null;
            }

            return (float) ($serie[0]['valor'] ?? 0) ?: null;

        } catch (\Throwable $e) {
            Log::debug("Error consultando mindicador.cl para UF ({$fecha}): " . $e->getMessage());
            return null;
        }
    }

    private function persistir(string $fecha, float $valor): void
    {
        ParidadUf::updateOrCreate(
            ['fecha' => $fecha],
            ['valor' => $valor]
        );
    }
}
