<?php

return [
    'api_url' => rtrim(env('TITO_API_URL', 'https://tito-api.onrender.com'), '/'),
    'graphql_endpoint' => env('TITO_GRAPHQL_ENDPOINT') ?: rtrim(env('TITO_API_URL', 'https://tito-api.onrender.com'), '/') . '/graphql',
];
