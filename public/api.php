<?php

declare(strict_types=1);

// Load configuration
require_once __DIR__ . '/../src/config/config.php';

// Autoload classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Initialize API components
$apiRouter = new Core\ApiRouter();
$request = new Core\Request();

// Load API routes
require_once __DIR__ . '/../src/routes/api.php';

// Dispatch API request
echo $apiRouter->dispatch($request->getMethod(), $request->getUri()); 