<?php

declare(strict_types=1);

// Kết nối database
try {
    $db = new PDO('sqlite:' . __DIR__ . '/database/app.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối database thất bại: " . $e->getMessage());
}

// Query dữ liệu từ bảng account_requirement
$query = "
    SELECT
        id_game_accounts,
        id_cp_requirement
    FROM account_requirement
    ORDER BY id_game_accounts, id_cp_requirement
";

try {
    $stmt = $db->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query thất bại: " . $e->getMessage());
}

// Hiển thị dữ liệu trong terminal
echo "=== DỮ LIỆU BẢNG ACCOUNT_REQUIREMENT ===\n\n";

if (empty($results)) {
    echo "Không có dữ liệu trong bảng account_requirement\n";
} else {
    echo "Tổng số bản ghi: " . count($results) . "\n\n";

    // Header
    printf("%-20s %-25s\n", "ID Game Account", "ID Computer Requirement");
    printf("%-20s %-25s\n", str_repeat("-", 20), str_repeat("-", 25));

    // Data
    foreach ($results as $row) {
        printf("%-20s %-25s\n",
            $row['id_game_accounts'],
            $row['id_cp_requirement']
        );
    }
}

echo "\n=== KẾT THÚC ===\n";