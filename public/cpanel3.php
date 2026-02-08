<?php
try {
    // Kết nối tới file SQLite
    $pdo = new PDO('sqlite:' . __DIR__ . '/data.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Tạo bảng Identification nếu chưa có
    $sql = "
    CREATE TABLE IF NOT EXISTS Identification (
        id INTEGER,
        uuid TEXT,
        label TEXT,
        note TEXT,
        FOREIGN KEY (id) REFERENCES logs(id)
    );
    ";

    $pdo->exec($sql);

    echo "✅ Bảng Identification đã được tạo thành công!";
} catch (PDOException $e) {
    echo "❌ Lỗi: " . $e->getMessage();
}
