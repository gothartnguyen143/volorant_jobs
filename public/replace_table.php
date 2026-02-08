<?php
// Kết nối SQLite
$dbPath = __DIR__ . '/data.db'; // đổi path tới file DB
try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Câu SQL
    $sql = "
        SELECT uid, total_capital_vnd, total_capital_usd, capital_count,
               first_capital_at, last_capital_at
        FROM view_capitals_by_uid
        ORDER BY total_capital_usd DESC
    ";

    // Thực thi query
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Hiển thị kết quả
    echo "<table border='1' cellpadding='6' cellspacing='0'>";
    echo "<tr>
            <th>UID</th>
            <th>Total VND</th>
            <th>Total USD</th>
            <th>Capital Count</th>
            <th>First Capital</th>
            <th>Last Capital</th>
          </tr>";

    foreach ($rows as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['uid']) . "</td>";
        echo "<td>" . htmlspecialchars($row['total_capital_vnd']) . "</td>";
        echo "<td>" . htmlspecialchars($row['total_capital_usd']) . "</td>";
        echo "<td>" . htmlspecialchars($row['capital_count']) . "</td>";
        echo "<td>" . htmlspecialchars($row['first_capital_at']) . "</td>";
        echo "<td>" . htmlspecialchars($row['last_capital_at']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (PDOException $e) {
    echo "DB error: " . $e->getMessage();
}
