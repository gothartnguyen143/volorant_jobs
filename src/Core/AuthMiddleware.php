<?php

declare(strict_types=1);

namespace Core;

use Services\AuthService;

class AuthMiddleware
{
  private AuthService $authService;

  public function __construct(AuthService $authService)
  {
    $this->authService = $authService;
  }

  public function handle(): bool
  {
    $auth = $this->authService->verifyAuth();

    if (!$auth) {
      header('Location: /admin/login');
      exit;
    }

    return true;
  }

  public function handleApi(): bool
  {
    $auth = $this->authService->verifyAuth();

    if (!$auth) {
      return false;
    }

    return true;
  }
}
