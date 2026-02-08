<?php

declare(strict_types=1);

namespace Core;

class Request
{
  public function getMethod(): string
  {
    return $_SERVER['REQUEST_METHOD'];
  }

  public function getUri(): string
  {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Chuẩn hóa: loại bỏ dấu "/" cuối nếu không phải root "/"
    if ($uri !== '/' && str_ends_with($uri, '/')) {
      $uri = rtrim($uri, '/');
    }

    return $uri;
  }

  public function get(string $key, mixed $default = null): mixed
  {
    return $_GET[$key] ?? $default;
  }

  public function post(string $key, mixed $default = null): mixed
  {
    return $_POST[$key] ?? $default;
  }

  public function all(): array
  {
    return array_merge($_GET, $_POST);
  }

  public function has(string $key): bool
  {
    return isset($_POST[$key]) || isset($_GET[$key]);
  }
}
