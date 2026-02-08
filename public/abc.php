<?php
// abc.php

// Trả kết quả dạng JSON
header('Content-Type: application/json; charset=utf-8');

// Thư mục gốc để lưu file (vd: /var/www/html/uploads)
$baseUploadDir = __DIR__ . '/uploads';

// Lấy tham số dir trên URL: ?dir=myfol
$dir = isset($_GET['dir']) ? $_GET['dir'] : 'default';

// Lọc ký tự lạ để tránh path traversal và ký tự lạ
$dir = preg_replace('/[^a-zA-Z0-9_-]/', '_', $dir);

// Thư mục đích cuối cùng: uploads/myfol
$targetDir = $baseUploadDir . '/' . $dir;

// Tạo thư mục nếu chưa có
if (!is_dir($targetDir)) {
    if (!mkdir($targetDir, 0775, true)) {
        http_response_code(500);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Không tạo được thư mục: ' . $targetDir
        ]);
        exit;
    }
}

// Kiểm tra xem có file upload không (Python dùng field name="file")
if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Không nhận được file (thiếu field "file")'
    ]);
    exit;
}

$file = $_FILES['file'];

// Kiểm tra lỗi upload
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Lỗi upload file, mã lỗi: ' . $file['error']
    ]);
    exit;
}

// Tên file gốc client gửi lên
$originalName = $file['name'];

// Lọc tên file cho an toàn
$originalName = basename($originalName);
$originalName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);

// Nếu muốn tránh trùng tên thì thêm timestamp / uniqid
$finalName = date('Ymd_His') . '_' . $originalName;

$targetPath = $targetDir . '/' . $finalName;

// Di chuyển file từ temp sang thư mục đích
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Không lưu được file trên server'
    ]);
    exit;
}

// Thành công
echo json_encode([
    'status'      => 'success',
    'message'     => 'Đã upload file thành công',
    'saved_path'  => $targetPath,
    'dir'         => $dir,
    'filename'    => $finalName
]);
