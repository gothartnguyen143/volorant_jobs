<?php
// Cấu hình DB (file SQLite sẽ nằm cùng thư mục)
$dbFile = __DIR__ . "/data.db";

try {
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Tạo bảng nếu chưa có
    $pdo->exec("CREATE TABLE IF NOT EXISTS logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        uuid TEXT NOT NULL,
        email TEXT NOT NULL,
        spaces INTEGER NOT NULL,
        amount REAL NOT NULL,
        time TEXT NOT NULL,
        serial TEXT NOT NULL
    )");
} catch (Exception $e) {
    http_response_code(500);
    echo "DB error: " . $e->getMessage();
    exit;
}

// Lấy tham số từ request (GET hoặc POST)
$uuid   = isset($_REQUEST['uuid'])   ? trim($_REQUEST['uuid'])   : '';
$email  = isset($_REQUEST['email'])  ? trim($_REQUEST['email'])  : '';
$spaces = isset($_REQUEST['spaces']) ? intval($_REQUEST['spaces']) : 0;
$amount = isset($_REQUEST['amount']) ? floatval($_REQUEST['amount']) : 0.0;
$time   = isset($_REQUEST['time'])   ? trim($_REQUEST['time']) : '';
$serial = isset($_REQUEST['serial']) ? trim($_REQUEST['serial']) : '';

// Validate cơ bản
if ($uuid === '' || $email === '' || $time === '' || $serial === '') {
    http_response_code(400);
    echo "Thiếu tham số uuid, email, time hoặc serial.";
    exit;
}

// Lưu vào DB
try {
    $stmt = $pdo->prepare("INSERT INTO logs (uuid, email, spaces, amount, time, serial) 
                           VALUES (:uuid, :email, :spaces, :amount, :time, :serial)");
    $stmt->execute([
        ":uuid"   => $uuid,
        ":email"  => $email,
        ":spaces" => $spaces,
        ":amount" => $amount,
        ":time"   => $time,
        ":serial" => $serial,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo "Insert error: " . $e->getMessage();
    exit;
}

// Trả về JSON xác nhận
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    "status" => "ok",
    "uuid"   => $uuid,
    "email"  => $email,
    "spaces" => $spaces,
    "amount" => $amount,
    "time"   => $time,
    "serial" => $serial
]);
