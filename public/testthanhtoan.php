<?php
// Cho phép mọi loại request và CORS (nếu cần)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Nếu là preflight request (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Lấy raw body của request
$body = file_get_contents('php://input');

// Ghi body vào file logtemp.txt
$logFile = __DIR__ . '/logtemp.txt';
file_put_contents(
    $logFile,
    "[" . date('Y-m-d H:i:s') . "]" . PHP_EOL . $body . PHP_EOL . str_repeat("-", 40) . PHP_EOL,
    FILE_APPEND
);

// In ra body luôn (trả về thẳng nội dung nhận được)
header('Content-Type: text/plain; charset=utf-8');
echo $body;
