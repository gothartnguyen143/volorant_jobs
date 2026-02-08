<?php

declare(strict_types=1);

namespace Controllers;

use Services\UserService;
use Services\RulesService;

class HomeController
{
  private $userService;
  private $rulesService;

  public function __construct(UserService $userService, RulesService $rulesService)
  {
    $this->userService = $userService;
    $this->rulesService = $rulesService;
  }

  public function showHomePage(): void
  {
    $admin = $this->userService->findAdmin();
    $rules = $this->rulesService->findRules();

    $data = [
      'admin' => $admin,
      'rules' => $rules
    ];
    extract($data);

    require_once __DIR__ . '/../views/home/page.php';
  }

  public function showIntroPage(): void
  {
    require_once __DIR__ . '/../views/intro/page.php';
  }
}
