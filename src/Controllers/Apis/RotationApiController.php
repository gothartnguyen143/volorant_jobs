<?php

declare(strict_types=1);

namespace Controllers\Apis;

use Services\RotationService;

class RotationApiController
{
  private $rotationService;

  public function __construct(RotationService $rotationService)
  {
    $this->rotationService = $rotationService;
  }

  /**
   * API: /api/v1/rotation/spin
   * Body: JSON { identifier: string }
   * Response: { success: bool, prize: null|{id,name}, message?: string }
   *
   * Comments:
   * - Controller chịu trách nhiệm nhận request, validate input cơ bản và gọi service.
   * - Mọi transaction / business logic chính được đặt trong `RotationService`.
   */
  public function spin(): array
  {
    // Hỗ trợ JSON body hoặc form-data
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $identifier = isset($input['identifier']) ? trim((string)$input['identifier']) : '';

    if ($identifier === '') {
      // Tạm thời dùng số mặc định khi người dùng không nhập
      $identifier = '0399793159';
    }

    try {
      // Gọi service thực hiện 1 lượt quay
      $result = $this->rotationService->performSpin($identifier);
      return $result;
    } catch (\Throwable $e) {
      $code = $e->getCode() ?: 500;
      http_response_code($code);
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }
  }

  // -------------------------
  // Prizes CRUD (merged here per request)
  // - index(): GET /api/v1/rotation/prizes
  // - show(id): GET /api/v1/rotation/prizes/{id}
  // - store(): POST /api/v1/rotation/prizes  (admin)
  // - update(id): PUT /api/v1/rotation/prizes/{id} (admin)
  // - destroy(id): DELETE /api/v1/rotation/prizes/{id} (admin)
  // Comments: controller chịu validate đơn giản, phần business nằm trong RotationService
  // -------------------------

  public function prizesIndex(): array
  {
    $onlyActive = isset($_GET['active']) && ($_GET['active'] === '1' || $_GET['active'] === 'true');
    $prizes = $this->rotationService->listPrizes($onlyActive);
    return ['prizes' => $prizes];
  }

  public function prizesShow(string $id): array
  {
    $prize = $this->rotationService->getPrize((int)$id);
    if (!$prize) {
      http_response_code(404);
      return ['success' => false, 'message' => 'Prize not found'];
    }
    return ['prize' => $prize];
  }

  public function prizesStore(): array
  {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    if (empty($input['name'])) {
      http_response_code(400);
      return ['success' => false, 'message' => 'Missing name'];
    }

    try {
      $id = $this->rotationService->createPrize($input);
      return ['success' => true, 'id' => $id];
    } catch (\Throwable $e) {
      http_response_code(500);
      return ['success' => false, 'message' => $e->getMessage()];
    }
  }

  public function prizesUpdate(string $id): array
  {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    try {
      $this->rotationService->updatePrize((int)$id, $input);
      return ['success' => true];
    } catch (\Throwable $e) {
      http_response_code(500);
      return ['success' => false, 'message' => $e->getMessage()];
    }
  }

  public function prizesDestroy(string $id): array
  {
    try {
      $this->rotationService->deletePrize((int)$id);
      return ['success' => true];
    } catch (\Throwable $e) {
      http_response_code(500);
      return ['success' => false, 'message' => $e->getMessage()];
    }
  }

  /**
   * GET /api/v1/admin/rotaions/spin-history
   * Return spin history with player identifier and prize name for admin UI
   */
  public function history(): array
  {
    try {
      $hist = $this->rotationService->listHistory();
      return ['history' => $hist];
    } catch (\Throwable $e) {
      http_response_code(500);
      return ['success' => false, 'message' => $e->getMessage()];
    }
  }

  /**
   * POST /api/v1/admin/rotation/update-all-player-turns
   * Body: JSON { total_turns: int }
   * Response: { success: bool, message?: string }
   */
  public function updateAllPlayerTurns(): array
  {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $totalTurns = isset($input['total_turns']) ? (int)$input['total_turns'] : 0;

    if ($totalTurns < 0) {
      http_response_code(400);
      return ['success' => false, 'message' => 'Total turns must be non-negative'];
    }

    try {
      $this->rotationService->updateAllPlayersTurns($totalTurns);
      return ['success' => true, 'message' => 'All player turns updated successfully'];
    } catch (\Throwable $e) {
      http_response_code(500);
      return ['success' => false, 'message' => $e->getMessage()];
    }
  }
}
