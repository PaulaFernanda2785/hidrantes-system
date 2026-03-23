<?php

return [
    'name' => env('APP_NAME', 'Sistema de Gestão de Hidrantes'),
    'env' => env('APP_ENV', 'production'),
    'debug' => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOL),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'America/Fortaleza',
];
