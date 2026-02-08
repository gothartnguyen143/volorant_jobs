<?php

declare(strict_types=1);

namespace Core;

class ApiRouter
{
  private array $routes = [];

  public function get(string $path, callable $handler): void
  {
    $this->routes['GET'][$path] = $handler;
  }

  public function post(string $path, callable $handler): void
  {
    $this->routes['POST'][$path] = $handler;
  }

  public function put(string $path, callable $handler): void
  {
    $this->routes['PUT'][$path] = $handler;
  }

  public function delete(string $path, callable $handler): void
  {
    $this->routes['DELETE'][$path] = $handler;
  }

  public function dispatch(string $method, string $uri): string
  {
    $path = parse_url($uri, PHP_URL_PATH);

    // Set JSON content type
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    // Handle preflight requests
    if ($method === 'OPTIONS') {
      http_response_code(200);
      return '';
    }

    // Check for exact match first
    if (isset($this->routes[$method][$path])) {
      try {
        $result = call_user_func($this->routes[$method][$path]);
        return json_encode($result, JSON_UNESCAPED_UNICODE);
      } catch (\Exception $e) {
        http_response_code(500);
        return json_encode([
          'error' => true,
          'message' => $e->getMessage()
        ]);
      }
    }

    // Check for dynamic routes
    foreach ($this->routes[$method] ?? [] as $routePath => $handler) {
      $pattern = $this->convertRouteToRegex($routePath);
      if (preg_match($pattern, $path, $matches)) {
        // Extract parameters
        $params = $this->extractParams($routePath, $path);
        foreach ($params as $key => $value) {
          $_GET[$key] = $value;
        }

        try {
          $result = call_user_func_array($handler, array_values($params));
          return json_encode($result, JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
          http_response_code(500);
          return json_encode([
            'error' => true,
            'message' => $e->getMessage()
          ]);
        }
      }
    }

    // Handle 404
    http_response_code(404);
    return json_encode([
      'error' => true,
      'message' => 'API endpoint not found'
    ]);
  }

  private function convertRouteToRegex(string $route): string
  {
    return '#^' . preg_replace('#\{([a-zA-Z0-9_]+)\}#', '([^/]+)', $route) . '$#';
  }

  private function extractParams(string $route, string $path): array
  {
    $params = [];
    $routeParts = explode('/', $route);
    $pathParts = explode('/', $path);

    foreach ($routeParts as $index => $routePart) {
      if (preg_match('#\{([a-zA-Z0-9_]+)\}#', $routePart, $matches)) {
        $paramName = $matches[1];
        $params[$paramName] = $pathParts[$index] ?? null;
      }
    }

    return $params;
  }
}
