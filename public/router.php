<?php

declare(strict_types=1);

// Router for PHP built-in server
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the file exists, serve it directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
  return false;
}

// Handle API routes
if (strpos($uri, '/api/') === 0) {
  require_once __DIR__ . '/api.php';
  exit;
}

// Otherwise, serve index.php for web routes
require_once __DIR__ . '/index.php';
