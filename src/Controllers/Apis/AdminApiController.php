<?php

declare(strict_types=1);

namespace Controllers\Apis;

use Services\FileService;
use Services\RulesService;
use Services\UserService;

class AdminApiController
{
  private $userService;
  private $rulesService;

  public function __construct(UserService $userService, RulesService $rulesService)
  {
    $this->userService = $userService;
    $this->rulesService = $rulesService;
  }

  public function updateAdminProfile(): array
  {
    if (!isset($_POST['adminData'])) {
      http_response_code(400);
      return [
        'success' => false,
        'message' => 'Thiếu dữ liệu admin'
      ];
    }

    // Lấy dữ liệu admin (giả sử client stringify JSON và append vào formData)
    $adminJson = $_POST['adminData'];
    $adminData = json_decode($adminJson, true);

    if (!is_array($adminData)) {
      http_response_code(400);
      return [
        'success' => false,
        'message' => 'Dữ liệu admin không hợp lệ'
      ];
    }

    try {
      $this->userService->updateAdminProfile($adminData);
    } catch (\Throwable $th) {
      if ($th instanceof \InvalidArgumentException) {
        http_response_code(400);
        return [
          'success' => false,
          'message' => $th->getMessage()
        ];
      }

      http_response_code(500);
      return [
        'success' => false,
        'message' => 'Lỗi hệ thống'
      ];
    }

    if (isset($_POST['rulesData'])) {
      $rulesData = $_POST['rulesData'];
      try {
        $this->rulesService->updateRules($rulesData);
      } catch (\Throwable $th) {
        http_response_code(500);
        return [
          'success' => false,
          'message' => 'Đã xảy ra lỗi khi cập nhật quy định thuê acc'
        ];
      }
    }

    if (isset($_POST['commitmentData'])) {
      $commitmentData = $_POST['commitmentData'];
      try {
        $this->rulesService->updateCommitment($commitmentData);
      } catch (\Throwable $th) {
        http_response_code(500);
        return [
          'success' => false,
          'message' => 'Đã xảy ra lỗi khi cập nhật cam kết mua acc'
        ];
      }
    }

    return [
      'success' => true,
    ];
  }

  public function updateWebUI(): array
  {
    $fileService = new FileService();

    if (!isset($_FILES['ui_file'])) {
      http_response_code(400);
      return [
        'success' => false,
        'message' => 'Thiếu dữ liệu đầu vào'
      ];
    }

    $file = $_FILES['ui_file'];
    // Xác định có phải video không
    $isVideo = in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), $fileService->getSupportedVideoExtensions());

    try {
      $result = $isVideo
        ? $fileService->saveUIVideo($file)
        : $fileService->saveUIImage($file);
    } catch (\Throwable $e) {
      http_response_code(500);
      return [
        'success' => false,
        'message' => 'Lỗi cập nhật UI: ' . $e->getMessage()
      ];
    }

    return [
      'success' => true,
      'message' => 'Cập nhật UI thành công!',
      'file_url' => $result['url']
    ];
  }
}
