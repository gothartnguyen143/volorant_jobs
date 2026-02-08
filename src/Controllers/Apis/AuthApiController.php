<?php

declare(strict_types=1);

namespace Controllers\Apis;

use Services\AuthService;

class AuthApiController
{
  private $authService;

  public function __construct(AuthService $authService)
  {
    $this->authService = $authService;
  }

  public function login(): array
  {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
      http_response_code(400);
      return [
        'success' => false,
        'message' => 'Tên người dùng và mật khẩu không được để trống'
      ];
    }

    $token = $this->authService->login($username, $password);

    if (!$token) {
      http_response_code(401);
      return [
        'success' => false,
        'message' => 'Tên người dùng hoặc mật khẩu không đúng'
      ];
    }

    $this->authService->setAuthCookie($token);
    return [
      'success' => true,
      'message' => 'Đăng nhập thành công'
    ];
  }

  public function logout(): array
  {
    $auth = $this->authService->verifyAuth();
    if (!$auth) {
      http_response_code(401);
      return [
        'success' => false,
        'message' => 'Người dùng chưa đăng nhập'
      ];
    }
    $this->authService->clearAuthCookie();
    return [
      'success' => true,
      'message' => 'Đăng xuất thành công'
    ];
  }
}
