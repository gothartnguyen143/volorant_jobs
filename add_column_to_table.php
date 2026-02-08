<?php

declare(strict_types=1);

// Kết nối CSDL
$db = new PDO('sqlite:' . __DIR__ . '/database/app.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
  $db->exec("
    ALTER TABLE sale_accounts
    ADD COLUMN sell_to_time DATETIME
  ");

  echo "Đã cập nhật thành công bảng sale_accounts với cột sell_to_time.";
} catch (PDOException $e) {
  echo "Lỗi khi cập nhật bảng: " . $e->getMessage();
}
