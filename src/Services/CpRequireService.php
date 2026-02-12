<?php

declare(strict_types=1);

namespace Services;

use PDO;

class CpRequireService
{
  private $db;

  public function __construct(PDO $db)
  {
    $this->db = $db;
  }

  /**
   * Fetch all computer requirements from the database.
   */
  public function getAllRequirements(): array
  {
    $stmt = $this->db->prepare("SELECT * FROM computer_requirements ORDER BY id ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get selected requirement IDs for a specific account.
   */
  public function getSelectedRequirementsForAccount(int $accountId): array
  {
    $stmt = $this->db->prepare("
      SELECT id_cp_requirement 
      FROM account_requirement 
      WHERE id_game_accounts = :accountId
    ");
    $stmt->execute(['accountId' => $accountId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  }

  /**
   * Get all selected requirements grouped by account ID.
   */
  public function getAllSelectedRequirementsGrouped(): array
  {
    $stmt = $this->db->prepare("
      SELECT id_game_accounts, id_cp_requirement 
      FROM account_requirement 
      ORDER BY id_game_accounts
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $grouped = [];
    foreach ($rows as $row) {
      $grouped[$row['id_game_accounts']][] = $row['id_cp_requirement'];
    }
    return $grouped;
  }

  /**
   * Toggle requirement for account: insert if checked, delete if unchecked.
   */
  public function toggleRequirementForAccount(int $accountId, int $requirementId, bool $checked): bool
  {
    if ($checked) {
      // Insert if not exists
      $stmt = $this->db->prepare("
        INSERT OR IGNORE INTO account_requirement (id_game_accounts, id_cp_requirement) 
        VALUES (:accountId, :requirementId)
      ");
      return $stmt->execute(['accountId' => $accountId, 'requirementId' => $requirementId]);
    } else {
      // Delete
      $stmt = $this->db->prepare("
        DELETE FROM account_requirement 
        WHERE id_game_accounts = :accountId AND id_cp_requirement = :requirementId
      ");
      return $stmt->execute(['accountId' => $accountId, 'requirementId' => $requirementId]);
    }
  }
}