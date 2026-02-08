<?php

declare(strict_types=1);

namespace Services;

use PDO;
use Utils\Helper;

class SaleAccountService
{
  private $db;
  const LIMIT_WITH_LOAD_MORE = 10;
  const LIMIT_WITH_PAGINATION = 5;

  public function __construct(PDO $db)
  {
    $this->db = $db;
  }

  public function countAll(): int
  {
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM sale_accounts");
    $stmt->execute();
    return (int) $stmt->fetchColumn();
  }

  // Fetch with pagination
  public function fetchAccountsWithPagination(
    int $page = 1,
    int $limit = self::LIMIT_WITH_PAGINATION
  ): array {
    $sql = "SELECT * FROM sale_accounts ORDER BY created_at DESC";

    $offset = ($page - 1) * $limit;
    $sql .= " LIMIT $limit OFFSET $offset";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Fetch with load more
  public function fetchAccountsWithLoadMore(
    ?int $lastId = null,
    ?string $status = null,
    ?string $search_term = null,
    ?string $letter = null
  ): array {
    $sql = "SELECT * FROM sale_accounts";
    $conditions = [];
    $params = [];

    // Load more logic theo id
    if ($lastId !== null) {
      $conditions[] = '(id < :last_id)';
      $params[':last_id'] = $lastId;
    }

    if ($letter !== null) {
      $conditions[] = 'letter = :letter';
      $params[':letter'] = $letter;
    }
    if ($status !== null) {
      $conditions[] = 'status = :status';
      $params[':status'] = $status;
    }
    if ($search_term !== null) {
      $conditions[] = '(`description` LIKE :search_term)';
      $params[':search_term'] = '%' . $search_term . '%';
    }

    if (!empty($conditions)) {
      $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    $order_condition = " ORDER BY created_at DESC, id DESC LIMIT " . self::LIMIT_WITH_LOAD_MORE;
    $sql .= $order_condition;

    $stmt = $this->db->prepare($sql);
    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value);
    }
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function addNewAccounts(array $data): ?int
  {
    $sql = "INSERT INTO sale_accounts (`status`, price, gmail, letter, `description`, avatar, created_at, sell_to_time)
            VALUES (:status, :price, :gmail, :letter, :description, :avatar, :created_at, :sell_to_time)";
    try {
      $this->db->beginTransaction();
      $stmt = $this->db->prepare($sql);

      $now = Helper::getNowWithTimezone();
      $insertedAccountId = null;

      foreach ($data as $row) {
        $status      = $row['status'] ?? null;
        $price       = $row['price'] ?? null;
        $gmail       = $row['gmail'] ?? null;
        $letter      = $row['letter'] ?? null;
        $description = $row['description'] ?? '';
        $avatar      = $row['avatar'] ?? null;
        $sell_to_time = $row['sell_to_time'] ?? null;

        if (!$status) {
          throw new \InvalidArgumentException("Trường trạng thái không được để trống.");
        }
        if (!$price) {
          throw new \InvalidArgumentException("Trường giá không được để trống.");
        }
        if (!$gmail) {
          throw new \InvalidArgumentException("Trường email không được để trống.");
        }
        if (!$letter) {
          throw new \InvalidArgumentException("Trường thư không được để trống.");
        }
        if (!$description) {
          throw new \InvalidArgumentException("Trường mô tả không được để trống.");
        }

        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':price', $price);
        $stmt->bindValue(':gmail', $gmail);
        $stmt->bindValue(':letter', $letter);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':avatar', $avatar);
        $stmt->bindValue(':created_at', $now);
        $stmt->bindValue(':sell_to_time', $sell_to_time);

        $stmt->execute();

        // Lấy ID của account vừa insert (SQLite)
        if ($insertedAccountId === null) {
          $insertedAccountId = $this->db->lastInsertId();
        }
      }

      $this->db->commit();

      return (int) $insertedAccountId;
    } catch (\PDOException $e) {
      $this->db->rollBack();

      // Các lỗi khác
      throw $e;
    }
  }

  public function updateAccount(int $accountId, array $data): void
  {
    // Kiểm tra tài khoản có tồn tại không
    $account = $this->findAccountById($accountId);
    if (!$account) {
      throw new \InvalidArgumentException("Tài khoản không tồn tại.");
    }

    $status      = $data['status'] ?? null;
    $price       = $data['price'] ?? null;
    $gmail       = $data['gmail'] ?? null;
    $letter      = $data['letter'] ?? null;
    $description = $data['description'] ?? null;
    $avatar      = $data['avatar'] ?? null;
    $sell_to_time = $data['sell_to_time'] ?? null;

    $updateFields = [];
    $params = [];

    if ($status !== null) {
      $updateFields[] = "`status` = :status";
      $params[':status'] = $status;
    }
    if ($price !== null) {
      $updateFields[] = "price = :price";
      $params[':price'] = $price;
    }
    if ($gmail !== null) {
      $updateFields[] = "gmail = :gmail";
      $params[':gmail'] = $gmail;
    }
    if ($letter !== null) {
      $updateFields[] = "letter = :letter";
      $params[':letter'] = $letter;
    }
    if ($description !== null) {
      $updateFields[] = "`description` = :description";
      $params[':description'] = $description;
    }
    if ($avatar !== null) {
      $updateFields[] = "avatar = :avatar";
      $params[':avatar'] = $avatar;
    }
    if ($sell_to_time !== null) {
      $updateFields[] = "sell_to_time = :sell_to_time";
      $params[':sell_to_time'] = $sell_to_time;
    }
    if (empty($updateFields)) {
      throw new \InvalidArgumentException("Không có trường nào để cập nhật.");
    }

    $sql = "UPDATE sale_accounts SET " . implode(', ', $updateFields) . " WHERE id = :id";
    $params[':id'] = $accountId;

    try {
      $this->db->beginTransaction();

      $stmt = $this->db->prepare($sql);
      foreach ($params as $param => $value) {
        $stmt->bindValue($param, $value);
      }

      $stmt->execute();
      $this->db->commit();
    } catch (\Throwable $e) {
      $this->db->rollBack();
      throw $e;
    }
  }

  public function getLatestAccount(): array
  {
    $stmt = $this->db->prepare("SELECT * FROM sale_accounts ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function findAccountById(int $accountId): ?array
  {
    $stmt = $this->db->prepare("SELECT * FROM sale_accounts WHERE id = :id");
    $stmt->bindValue(':id', $accountId);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
  }

  public function deleteAccount(int $accountId): void
  {
    if (!$this->findAccountById($accountId)) {
      throw new \InvalidArgumentException("Tài khoản không tồn tại.");
    }

    $stmt = $this->db->prepare("DELETE FROM sale_accounts WHERE id = :id");
    $stmt->bindValue(':id', $accountId);
    $stmt->execute();
  }

  public function refreshAccount(int $accountId): array
  {
    $account = $this->findAccountById($accountId);
    if (!$account) {
      throw new \InvalidArgumentException("Tài khoản không tồn tại.");
    }

    return $account;
  }

  public function switchLetterQuickly(int $accountId): void
  {
    $account = $this->findAccountById($accountId);
    if (!$account) {
      throw new \InvalidArgumentException("Tài khoản không tồn tại.");
    }

    $letter = $account['letter'];
    $newLetter = $letter === 'A' ? 'B' : 'A';

    $this->updateAccount($accountId, ['letter' => $newLetter]);
  }
}
