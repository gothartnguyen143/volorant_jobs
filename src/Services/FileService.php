<?php

declare(strict_types=1);

namespace Services;

use Utils\DevLogger;

class FileService
{
  private $supportedImageExtensions = ['jpg', 'jpeg', 'png', 'webp'];
  private $supportedVideoExtensions = ['mp4', 'webm', 'ogg'];
  private $maxSize = 5 * 1024 * 1024; // 5MB

  public function __construct() {}

  public function getSupportedVideoExtensions(): array
  {
    return $this->supportedVideoExtensions;
  }

  public function normalizeFiles(array $files): array
  {
    $normalized = [];
    $fileCount = count($files['name']);
    for ($i = 0; $i < $fileCount; $i++) {
      $normalized[] = [
        'name'     => $files['name'][$i],
        'type'     => $files['type'][$i],
        'tmp_name' => $files['tmp_name'][$i],
        'error'    => $files['error'][$i],
        'size'     => $files['size'][$i],
      ];
    }
    return $normalized;
  }

  public function saveAvatarImages(array $files, int $accountId): array
  {
    $fileNames = [];
    foreach ($files as $file) {
      $fileNames[] = $this->saveAvatarImage($file, $accountId)['fileName'];
    }
    return ['fileNames' => $fileNames];
  }

  public function createAccountAvatarFileName(int $accountId, string $extension): string
  {
    return 'account_' . $accountId . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
  }

  public function saveAvatarImage($file, int $accountId): array
  {
    $uploadDir = __DIR__ . '/../../public/images/account/';

    // Tạo thư mục nếu chưa tồn tại
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0755, true);
    }

    // Lấy thông tin file
    $fileInfo = pathinfo($file['name']);
    $extension = strtolower($fileInfo['extension']);

    // Kiểm tra định dạng file hợp lệ
    if (!in_array($extension, $this->supportedImageExtensions)) {
      throw new \InvalidArgumentException('Định dạng file không được hỗ trợ. Chỉ chấp nhận: JPG, PNG, WEBP.');
    }

    // Kiểm tra kích thước file (tối đa 5MB)
    if ($file['size'] > $this->maxSize) {
      throw new \InvalidArgumentException('Kích thước file quá lớn. Tối đa 5MB.');
    }

    // Tạo tên file mới
    $fileName = $this->createAccountAvatarFileName($accountId, $extension);
    $filePath = $uploadDir . $fileName;

    // Di chuyển file upload vào thư mục đích
    $result = move_uploaded_file($file['tmp_name'], $filePath);
    if (!$result) {
      throw new \RuntimeException('Không thể lưu file ảnh.');
    }

    return [
      'fileName' => $fileName,
      'filePath' => $filePath,
    ];
  }

  public function deleteAvatarImages(array $fileNames): void
  {
    foreach ($fileNames as $fileName) {
      $this->deleteAvatarImage($fileName);
    }
  }

  public function deleteAvatarImage(string $fileName): void
  {
    if (empty($fileName)) {
      return;
    }

    $filePath = __DIR__ . '/../../public/images/account/' . $fileName;

    if (file_exists($filePath)) {
      unlink($filePath);
    }
  }

  /**
   * Hàm dùng chung để lưu file.
   */
  private function saveFileTo(array $file, string $destinationDir, array $allowedExtensions, int $maxSizeInBytes): array
  {
    // Tạo thư mục nếu chưa có
    if (!is_dir($destinationDir)) {
      mkdir($destinationDir, 0755, true);
    }

    $fileInfo = pathinfo($file['name']);
    $extension = strtolower($fileInfo['extension'] ?? '');

    if (!in_array($extension, $allowedExtensions)) {
      throw new \InvalidArgumentException('Định dạng file không được hỗ trợ.');
    }

    if ($file['size'] > $maxSizeInBytes) {
      throw new \InvalidArgumentException('Kích thước file vượt quá giới hạn cho phép.');
    }

    $fileName = 'ui_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $filePath = rtrim($destinationDir, '/') . '/' . $fileName;

    $result = move_uploaded_file($file['tmp_name'], $filePath);
    if (!$result) {
      throw new \RuntimeException('Không thể lưu file.');
    }

    return [
      'fileName' => $fileName,
      'filePath' => $filePath,
      'url' => '/web-thue-acc-valorant' . str_replace(__DIR__ . '/../../public', '', $filePath),
    ];
  }

  /**
   * Lưu ảnh vào public/images/UI
   */
  public function saveUIImage(array $file): array
  {
    $uploadDir = __DIR__ . '/../../public/images/UI/';
    $maxSize = 5 * 1024 * 1024; // 5MB

    return $this->saveFileTo($file, $uploadDir, $this->supportedImageExtensions, $maxSize);
  }

  /**
   * Lưu video vào public/videos/UI
   */
  public function saveUIVideo(array $file): array
  {
    $uploadDir = __DIR__ . '/../../public/videos/UI/';
    $maxSize = 100 * 1024 * 1024; // 100MB

    return $this->saveFileTo($file, $uploadDir, $this->supportedVideoExtensions, $maxSize);
  }
}