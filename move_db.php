<?php

declare(strict_types=1);

// Kết nối CSDL
$db = new PDO('sqlite:' . __DIR__ . '/database/app.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
  // 1. Tạo bảng mới với CHECK đã thêm giá trị "Check"
  $db->exec("
        CREATE TABLE IF NOT EXISTS game_accounts_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            acc_name TEXT NOT NULL,
            rank TEXT NOT NULL,
            game_code TEXT NOT NULL,
            [status] TEXT NOT NULL DEFAULT 'Rảnh' CHECK ([status] IN ('Rảnh', 'Bận', 'Check')),
            [description] TEXT,
            device_type TEXT NOT NULL CHECK (device_type IN ('Máy nhà', 'Tất cả')),
            avatar TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            rent_to_time DATETIME,
            rent_from_time DATETIME
        );
    ");

  // 2. Sao chép dữ liệu từ bảng cũ sang bảng mới
  $db->exec("
        INSERT INTO game_accounts_new (
            id, acc_name, rank, game_code, status, description, device_type,
            avatar, created_at, updated_at, rent_to_time, rent_from_time
        )
        SELECT
            id, acc_name, rank, game_code, status, description, device_type,
            avatar, created_at, updated_at, rent_to_time, rent_from_time
        FROM game_accounts;
    ");

  // 3. Xóa bảng cũ
  $db->exec("DROP TABLE game_accounts");

  // 4. Đổi tên bảng mới thành tên cũ
  $db->exec("ALTER TABLE game_accounts_new RENAME TO game_accounts");

  echo "Đã cập nhật thành công bảng game_accounts với giá trị 'Check' trong cột status.";
} catch (PDOException $e) {
  echo "Lỗi khi cập nhật bảng: " . $e->getMessage();
}
