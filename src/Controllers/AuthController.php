<?php

declare(strict_types=1);

namespace Controllers;

use Services\AuthService;

class AuthController
{
  private AuthService $authService;

  public function __construct(AuthService $authService)
  {
    $this->authService = $authService;
  }

  public function showLoginPage(): void
  {
    // Check if already logged in
    $auth = $this->authService->verifyAuth();
    if ($auth) {
      header('Location: /admin/manage-game-accounts');
      exit;
    }

    require_once __DIR__ . '/../views/admin/login/page.php';
  }
}
