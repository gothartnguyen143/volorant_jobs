<?php
function custom_md5($input) {
    $prefix = "natproo!!@@@";
    $suffix = "bchshsj!@@!!@";
    return md5($prefix . $input . $suffix);
}

// Lấy tham số từ query string
$uid  = $_GET['uid']  ?? null;
$pass = $_GET['pass'] ?? null;

if (!$uid || !$pass) {
    die("Thiếu tham số uid hoặc pass");
}

if ($pass != "ncnksk12e31a!!!!!") {
    echo "Sai mật khẩu";
    exit;
}

// Tạo hash từ uid
$hash = custom_md5($uid);

$result = [
    'hash' => $hash
];

header('Content-Type: application/json');
echo json_encode($result);
