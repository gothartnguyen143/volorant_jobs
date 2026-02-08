<?php

declare(strict_types=1);

namespace Services;

use PDO;

class RulesService
{
  private $db;

  public function __construct(PDO $db)
  {
    $this->db = $db;
  }

  public function findRules(): array
  {
    $stmt = $this->db->prepare("SELECT * FROM rules LIMIT 1");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function updateRules(string $rulesData): void
  {
    $stmt = $this->db->prepare("UPDATE rules SET rent_acc_rules = :rulesData");
    $stmt->execute(['rulesData' => $rulesData]);
  }

  public function updateCommitment(string $commitmentData): void
  {
    $stmt = $this->db->prepare("UPDATE rules SET commitment = :commitmentData");
    $stmt->execute(['commitmentData' => $commitmentData]);
  }
}
