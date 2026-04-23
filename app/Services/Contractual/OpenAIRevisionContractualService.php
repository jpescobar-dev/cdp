<?php

namespace App\Services\Contractual;

use App\Models\RevisionContractual;
use Illuminate\Support\Facades\Http;
use App\Services\Contractual\PromptRevisionContractualBuilderService;

class OpenAIRevisionContractualService
{
    public function analizar(RevisionContractual $revision): array
    {
        $prompt = app(PromptRevisionContractualBuilderService::class)->build($revision);

        $response = Http::withToken(config('services.openai.key'))
            ->baseUrl(config('services.openai.base_url'))
            ->withOptions([
                'verify' => false, // temporal en local
            ])
            ->timeout(120)
            ->post('/responses', [
                'model' => config('services.openai.model'),
                'input' => $prompt,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Error al consultar OpenAI: ' . $response->body());
        }

        return $response->json();
    }
}