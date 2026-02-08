<?php

declare(strict_types=1);

namespace Services;

use PDO;

class UserService
{
  private $db;

  public function __construct(PDO $db)
  {
    $this->db = $db;
  }

  public function findAdmin(): array
  {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE role = 'ADMIN'");
    $stmt->execute();
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    return $res;
  }

  public function findAdminById(int $id): array
  {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id AND role = 'ADMIN'");
    $stmt->execute(['id' => $id]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    return $res;
  }

  public function updateAdminProfile(array $data): void
  {
    $admin = $this->findAdmin();
    if (!$admin) {
      throw new \RuntimeException("Admin không tồn tại.", 500);
    }

    $fields = [];
    $params = [];

    if (isset($data['password'])) {
      $password = $data['password'];
      if ($password && $password !== "") {
        $fields[] = "password = :password";
        $params['password'] = password_hash($password, PASSWORD_DEFAULT);
      }
    }

    foreach ($data as $key => $value) {
      if ($value && in_array($key, ['username', 'full_name', 'phone', 'facebook_link', 'zalo_link'])) {
        $fields[] = "$key = :$key";
        $params[$key] = $value;
      }
    }

    if (!empty($fields)) {
      $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE role = 'ADMIN'";

      try {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $this->db->commit();
      } catch (\Throwable $e) {
        $this->db->rollBack();
        throw $e;
      }
    }
  }
}
