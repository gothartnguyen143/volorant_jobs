<?php

declare(strict_types=1);

namespace Services;

use PDO;

class AuthService
{
  private PDO $db;
  private JwtService $jwtService;

  public function __construct(PDO $db, JwtService $jwtService)
  {
    $this->db = $db;
    $this->jwtService = $jwtService;
  }

  public function login(string $username, string $password): ?string
  {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username AND role = 'ADMIN'");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
      return null;
    }

    $payload = [
      'user_id' => $user['id'],
      'username' => $user['username'],
      'role' => $user['role']
    ];

    return $this->jwtService->generateToken($payload);
  }

  public function verifyAuth(): ?array
  {
    $token = $this->getTokenFromRequest();

    if (!$token) {
      return null;
    }

    $payload = $this->jwtService->verifyToken($token);

    if (!$payload) {
      return null;
    }

    // Verify user still exists in database
    $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id AND role = 'ADMIN'");
    $stmt->execute(['id' => $payload['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      return null;
    }

    return [
      'user' => $user,
      'jwt_payload' => $payload
    ];
  }

  private function getTokenFromRequest(): ?string
  {
    // Check Authorization header
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
      $authHeader = $headers['Authorization'];
      if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        return $matches[1];
      }
    }

    // Check cookie
    if (isset($_COOKIE['admin_token'])) {
      return $_COOKIE['admin_token'];
    }

    return null;
  }

  public function setAuthCookie(string $token): void
  {
    setcookie('admin_token', $token, time() + 3600, '/', '', false, true);
  }

  public function clearAuthCookie(): void
  {
    setcookie('admin_token', '', time() - 3600, '/');
  }
}
