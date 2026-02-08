<?php

namespace Utils;

class DevLogger
{
  public static function log(string $message)
  {
    $logFile = __DIR__ . '/../../logs/dev.log';
    $logMessage = '>>> ' . date('Y-m-d H:i:s') . ' - ' . self::formatJson($message) . PHP_EOL;

    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
      mkdir($logDir, 0755, true);
    }

    file_put_contents($logFile, $logMessage, FILE_APPEND);
  }

  public static function formatJson(string $message): string
  {
    // Kiểm tra xem message có phải là JSON hợp lệ không
    $decoded = json_decode($message, true);

    if (json_last_error() === JSON_ERROR_NONE) {
      // Nếu là JSON hợp lệ, format lại với indent 2 spaces
      return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    // Nếu không phải JSON, trả về message gốc
    return $message;
  }

  public static function logArray(string $title, array $arr)
  {
    error_log($title . ': ' . print_r($arr, true));
  }
}
