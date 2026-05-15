<?php

return [
    'default_model' => env('OPENAI_MODEL', 'gpt-5.5-thinking'),
    'timeout' => (int) env('AGENTES_TIMEOUT', 120),
    'max_retries' => (int) env('AGENTES_MAX_RETRIES', 2),

    'log_prompts' => filter_var(env('AGENTES_LOG_PROMPTS', true), FILTER_VALIDATE_BOOLEAN),
    'log_responses' => filter_var(env('AGENTES_LOG_RESPONSES', true), FILTER_VALIDATE_BOOLEAN),

    'modelos' => [
        'clasificador_cdp' => env('AGENTE_CLASIFICADOR_MODEL', env('OPENAI_MODEL', 'gpt-5.5-thinking')),
        'lector_documentos' => env('AGENTE_LECTOR_DOCUMENTOS_MODEL', env('OPENAI_MODEL', 'gpt-5.5-thinking')),
        'redactor_cdp' => env('AGENTE_CDP_MODEL', env('OPENAI_MODEL', 'gpt-5.5-thinking')),
        'validador_cdp' => env('AGENTE_VALIDADOR_MODEL', env('OPENAI_MODEL', 'gpt-5.5-thinking')),
        'redactor_respuesta' => env('AGENTE_REDACTOR_MODEL', env('OPENAI_MODEL', 'gpt-5.5-thinking')),
    ],

    'usuarios_tecnicos' => [
        'orquestador' => env('AGENTE_ORQUESTADOR_USER', 'agente.orquestador'),
        'extractor' => env('AGENTE_EXTRACTOR_USER', 'agente.extractor.mesa_ayuda'),
        'importador' => env('AGENTE_IMPORTADOR_USER', 'agente.importador_json'),
        'clasificador' => env('AGENTE_CLASIFICADOR_USER', 'agente.clasificador_cdp'),
        'lector_documentos' => env('AGENTE_LECTOR_DOCUMENTOS_USER', 'agente.lector_documentos'),
        'redactor_cdp' => env('AGENTE_REDACTOR_CDP_USER', 'agente.redactor_cdp'),
        'validador_cdp' => env('AGENTE_VALIDADOR_CDP_USER', 'agente.validador_cdp'),
        'respuesta_mesa_ayuda' => env('AGENTE_RESPUESTA_MESA_AYUDA_USER', 'agente.respuesta_mesa_ayuda'),
    ],
];
