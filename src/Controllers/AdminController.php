<?php

declare(strict_types=1);

namespace Controllers;

use Services\UserService;
use Services\AuthService;
use Services\RulesService;
use Services\GameAccountService;

class AdminController
{
  private $userService;
  private $authService;
  private $rulesService;
  private $gameAccountService;

  public function __construct(
    UserService $userService,
    AuthService $authService,
    RulesService $rulesService,
    GameAccountService $gameAccountService
  ) {
    $this->userService = $userService;
    $this->authService = $authService;
    $this->rulesService = $rulesService;
    $this->gameAccountService = $gameAccountService;
  }

  public function showManageGameAccountsPage(): void
  {
    $auth = $this->authService->verifyAuth();
    $admin = $this->userService->findAdminById((int)$auth['user']['id']);

    $ranks = $this->gameAccountService->fetchAccountRankTypes();

    $data = [
      'admin' => $admin,
      'ranks' => $ranks
    ];
    extract($data);

    require_once __DIR__ . '/../views/admin/manage_game_accounts/page.php';
  }

  public function showProfilePage(): void
  {
    $auth = $this->authService->verifyAuth();
    $admin = $this->userService->findAdminById($auth['user']['id']);
    $rules = $this->rulesService->findRules();

    $data = [
      'admin' => $admin,
      'auth' => $auth,
      'rules' => $rules
    ];
    extract($data);

    require_once __DIR__ . '/../views/admin/profile/page.php';
  }

  public function showSaleAccountsPage(): void
  {
    $auth = $this->authService->verifyAuth();
    $admin = $this->userService->findAdminById($auth['user']['id']);

    $data = [
      'admin' => $admin,
    ];
    extract($data);

    require_once __DIR__ . '/../views/admin/sale_accounts/page.php';
  }

  public function showRotationPage(): void
  {
    // verify admin auth and load admin data similar to other admin pages
    $auth = $this->authService->verifyAuth();
    $admin = $this->userService->findAdminById($auth['user']['id']);

    $data = [
      'admin' => $admin,
      'auth' => $auth
    ];
    extract($data);

    // Render admin rotation management page
    require_once __DIR__ . '/../views/admin/rotation/page.php';
  }

  public function showRotationHistoryPage(): void
  {
    $auth = $this->authService->verifyAuth();
    $admin = $this->userService->findAdminById($auth['user']['id']);

    $data = [
      'admin' => $admin,
      'auth' => $auth
    ];
    extract($data);

    require_once __DIR__ . '/../views/admin/rotation/rotation_history/page.php';
  }
}
