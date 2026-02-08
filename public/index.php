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

// Initialize core components
$router = new Core\Router();
$request = new Core\Request();

// Load routes
require_once __DIR__ . '/../src/routes/web.php';

// Dispatch request
echo $router->dispatch($request->getMethod(), $request->getUri());
