return [

    'paths' => ['api/*', 'auth/*', '*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],   // Nếu muốn bảo mật thì sửa sau
    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
