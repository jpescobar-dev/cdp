// Agregar en config/services.php

'openai' => [
    'key' => env('OPENAI_API_KEY'),
    'model' => env('OPENAI_MODEL', 'gpt-5.4'),
    'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
],
