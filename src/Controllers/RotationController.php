<?php

declare(strict_types=1);

namespace Controllers;

use Services\RotationService;

class RotationController
{
  private $rotationService;

  public function __construct(RotationService $rotationService)
  {
    $this->rotationService = $rotationService;
  }

  /**
   * Render trang quay số may mắn với danh sách prizes từ DB.
   */
  public function showPage(): void
  {
    try {
      $prizes = $this->rotationService->getAvailablePrizes(); // Chỉ lấy active prizes có quantity > 0

      // Chuẩn bị dữ liệu cho view
      $segments = [];
      $colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8', '#FFD93D', '#6BCB77', '#A8E6CF']; // Màu mặc định nếu cần
      foreach ($prizes as $index => $prize) {
        $segments[] = [
          'label' => $prize['name'],
          'color' => $colors[$index % count($colors)] ?? '#CCCCCC', // Lặp màu nếu nhiều hơn
        ];
      }

      $segmentCount = count($segments);
      $segmentAngle = ($segmentCount > 0) ? 360 / $segmentCount : 0;
      $defaultPrize = $segments[0]['label'] ?? 'Sẵn sàng';

      // Extract để view sử dụng
      extract(compact('segments', 'segmentCount', 'segmentAngle', 'defaultPrize'));

      require_once __DIR__ . '/../views/home/rotation.php';
    } catch (\Throwable $e) {
      // Debug: hiển thị lỗi nếu có
      echo "Error in showPage: " . $e->getMessage();
      exit;
    }
  }

  /**
   * API endpoint để giảm quantity của một prize (dùng cho admin hoặc sau quay).
   * Nhận JSON: { "prizeId": int }
   * Trả về JSON: { success: bool, message: string }
   */
  public function decreaseQuantity(): array
  {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $prizeId = (int)($input['prizeId'] ?? 0);

    if ($prizeId <= 0) {
      http_response_code(400);
      return ['success' => false, 'message' => 'Valid prizeId is required'];
    }

    try {
      $success = $this->rotationService->decreasePrizeQuantity($prizeId);
      if ($success) {
        return ['success' => true, 'message' => 'Quantity decreased successfully'];
      } else {
        http_response_code(400);
        return ['success' => false, 'message' => 'Cannot decrease quantity (prize not found or quantity <= 0)'];
      }
    } catch (\Throwable $e) {
      http_response_code(500);
      return ['success' => false, 'message' => 'Internal server error'];
    }
  }
}
