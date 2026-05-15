<?php

return [
    'url' => env('MESA_AYUDA_URL', 'http://mesaayuda.intranet.pjud/mesa_ayuda/index.php'),
    'user' => env('MESA_AYUDA_USER'),
    'password' => env('MESA_AYUDA_PASSWORD'),

    'headless' => env('MESA_AYUDA_HEADLESS', false),
    'max_folios' => env('MESA_AYUDA_MAX_FOLIOS', 0),

    'solo_lectura' => env('MESA_AYUDA_SOLO_LECTURA', true),
    'permitir_respuesta' => env('MESA_AYUDA_PERMITIR_RESPUESTA', false),
    'permitir_descarga_adjuntos' => env('MESA_AYUDA_PERMITIR_DESCARGA_ADJUNTOS', true),

    'timeout_proceso' => env('MESA_AYUDA_TIMEOUT_PROCESO', 900),

    'extractor' => [
        'script' => env('MESA_AYUDA_EXTRACTOR_SCRIPT', 'tests-playwright/extraer-json-minimo.cjs'),
    ],

    'rutas' => [
        'pruebas' => env('MESA_AYUDA_PRUEBAS_PATH', 'app/mesa-ayuda/pruebas'),
        'adjuntos' => env('MESA_AYUDA_ADJUNTOS_PATH', 'app/mesa-ayuda/adjuntos'),
    ],
];
