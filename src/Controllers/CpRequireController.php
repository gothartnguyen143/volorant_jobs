<?php

declare(strict_types=1);

namespace Controllers;

use Services\CpRequireService;

class CpRequireController
{
  private $cpRequireService;

  public function __construct(CpRequireService $cpRequireService)
  {
    $this->cpRequireService = $cpRequireService;
  }

  /**
   * API endpoint to fetch all computer requirements and selected grouped by account.
   * Returns JSON: { requirements: [...], selected: {accountId: [reqIds]} }
   */
  public function index(): array
  {
    try {
      $requirements = $this->cpRequireService->getAllRequirements();
      $selected = $this->cpRequireService->getAllSelectedRequirementsGrouped();
      return ['requirements' => $requirements, 'selected' => $selected];
    } catch (\Throwable $e) {
      http_response_code(500);
      return ['success' => false, 'message' => 'Internal server error'];
    }
  }

  /**
   * API endpoint to fetch selected requirements for a specific account.
   * Returns JSON: { selected: [id1, id2, ...] }
   */
  public function show(string $accountId): array
  {
    try {
      $selected = $this->cpRequireService->getSelectedRequirementsForAccount((int)$accountId);
      return ['selected' => $selected];
    } catch (\Throwable $e) {
      http_response_code(500);
      return ['success' => false, 'message' => 'Internal server error'];
    }
  }

  /**
   * API endpoint to toggle requirement for account.
   * Body: { accountId: int, requirementId: int, checked: bool }
   */
  public function toggle(): array
  {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $accountId = (int)($input['accountId'] ?? 0);
    $requirementId = (int)($input['requirementId'] ?? 0);
    $checked = (bool)($input['checked'] ?? false);

    if ($accountId <= 0 || $requirementId <= 0) {
      http_response_code(400);
      return ['success' => false, 'message' => 'Invalid accountId or requirementId'];
    }

    try {
      $success = $this->cpRequireService->toggleRequirementForAccount($accountId, $requirementId, $checked);
      if ($success) {
        return ['success' => true];
      } else {
        http_response_code(500);
        return ['success' => false, 'message' => 'Failed to toggle'];
      }
    } catch (\Throwable $e) {
      http_response_code(500);
      return ['success' => false, 'message' => $e->getMessage()];
    }
  }
}