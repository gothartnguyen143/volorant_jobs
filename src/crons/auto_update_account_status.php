<?php

declare(strict_types=1);

try {
  // Kết nối CSDL SQLite
  $db = new PDO('sqlite:' . __DIR__ . '/../..' . '/database/app.sqlite');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Cập nhật status thành 'Rảnh' nếu rent_to_time < CURRENT_TIMESTAMP
  $sql = "
    UPDATE game_accounts
    SET `status` = 'Check',
        updated_at = :now,
        rent_from_time = NULL,
        rent_to_time = NULL
    WHERE rent_to_time IS NOT NULL
      AND rent_to_time < CURRENT_TIMESTAMP
  ";

  $affected = $db->exec($sql);

  echo "Đã cập nhật {$affected} dòng có status = 'Rảnh' theo điều kiện rent_to_time < thời gian hiện tại.";
} catch (Exception $e) {
  echo "Lỗi khi cập nhật trạng thái acc: " . $e->getMessage();
}
