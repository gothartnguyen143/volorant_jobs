<?php
/****************************************************
 * api_update_failure.php
 *
 * Chức năng:
 *  - ĐÁNH LỖI:  POST { id, password, reason }
 *      -> Đặt isSuccess=0, reason=<reason> cho toàn bộ NHÓM
 *  - HỦY LỖI:   POST { id, password, undo=1 }
 *      -> Đặt isSuccess=1, reason=NULL cho toàn bộ NHÓM
 *
 * NHÓM xác định theo: cùng (uuid, serial), sort theo time ASC,
 * các bản ghi liên tiếp có khoảng cách thời gian <= MERGE_GAP_MINUTES
 * (mặc định 90 phút) thì thuộc cùng nhóm. Nhóm lấy theo bản ghi target id.
 *
 * Trả về JSON:
 *   { ok: true, action: "fail"|"undo", updated_count, updated_ids, group_info:{...} }
 *
 * Yêu cầu:
 *  - PHP >= 7.4
 *  - PDO SQLite bật sẵn
 ****************************************************/

ini_set('display_errors', 1);
error_reporting(E_ALL);

/** ------------------ Cấu hình ------------------ **/
const PASSWORD_PLAIN       = 'kiemtienthoi1234';
const MERGE_GAP_MINUTES    = 150;
$dbFile = __DIR__ . '/data.db';

// (Tùy chọn) Bật CORS khi cần gọi chéo domain
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: POST, OPTIONS');
// header('Access-Control-Allow-Headers: Content-Type');
// if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

/** ------------------ Helper ------------------ **/
function json_out(int $statusCode, array $payload): void {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    exit;
}
function dt_parse(string $ts): ?DateTime {
    try { return new DateTime($ts); } catch (Exception $e) { return null; }
}
function minutes_diff(DateTime $a, DateTime $b): float {
    return abs(($a->getTimestamp() - $b->getTimestamp()) / 60.0);
}
function sanitize_reason(?string $s): string {
    if ($s === null) return '';
    $s = trim($s);
    // Giới hạn độ dài để tránh spam/cột quá dài (tuỳ bạn chỉnh)
    if (function_exists('mb_substr')) $s = mb_substr($s, 0, 500);
    else $s = substr($s, 0, 500);
    return $s;
}

/** ------------------ Validate input ------------------ **/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(405, ['ok'=>false, 'error'=>'Method Not Allowed']);
}
if (!file_exists($dbFile)) {
    json_out(500, ['ok'=>false, 'error'=>'Không tìm thấy file DB.']);
}

$id       = isset($_POST['id'])       ? intval($_POST['id']) : 0;
$password = isset($_POST['password']) ? (string)$_POST['password'] : '';
$undo     = isset($_POST['undo'])     ? (string)$_POST['undo'] : '';
$reason   = isset($_POST['reason'])   ? sanitize_reason((string)$_POST['reason']) : '';

if ($password !== PASSWORD_PLAIN) {
    json_out(403, ['ok'=>false, 'error'=>'Sai mật khẩu.']);
}
if ($id <= 0) {
    json_out(400, ['ok'=>false, 'error'=>'Thiếu hoặc sai id.']);
}
$action = ($undo === '1') ? 'undo' : 'fail';
if ($action === 'fail' && $reason === '') {
    json_out(400, ['ok'=>false, 'error'=>'Lý do lỗi không được để trống.']);
}

/** ------------------ Main logic ------------------ **/
try {
    $pdo = new PDO('sqlite:' . $dbFile, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // 1) Lấy bản ghi target
    $stmt = $pdo->prepare("SELECT id, uuid, serial, time FROM logs WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        json_out(404, ['ok'=>false, 'error'=>'Không tìm thấy giao dịch với id đã cho.']);
    }
    $uuid   = (string)$row['uuid'];
    $serial = (string)$row['serial'];
    $time   = (string)$row['time'];
    $tTarget= dt_parse($time);
    if (!$tTarget) {
        json_out(500, ['ok'=>false, 'error'=>'Thời gian của giao dịch không hợp lệ.']);
    }

    // 2) Lấy toàn bộ bản ghi cùng uuid+serial theo time ASC
    $stmt = $pdo->prepare("
        SELECT id, time
        FROM logs
        WHERE uuid = :uuid AND serial = :serial
        ORDER BY time ASC, id ASC
    ");
    $stmt->execute([':uuid'=>$uuid, ':serial'=>$serial]);
    $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$all) {
        json_out(500, ['ok'=>false, 'error'=>'Không thể tải nhóm giao dịch liên quan.']);
    }

    // 3) Tìm vị trí target & thời điểm của từng phần tử
    $idxTarget = -1;
    $times = [];
    foreach ($all as $i => $r) {
        $tt = dt_parse((string)$r['time']);
        if (!$tt) continue;
        $times[$i] = $tt;
        if (intval($r['id']) === $id) $idxTarget = $i;
    }
    if ($idxTarget < 0) {
        json_out(500, ['ok'=>false, 'error'=>'Không xác định được vị trí giao dịch mục tiêu trong chuỗi thời gian.']);
    }

    // 4) Nới rộng nhóm theo quy tắc 90 phút
    $left = $idxTarget;
    for ($i = $idxTarget - 1; $i >= 0; $i--) {
        $gap = minutes_diff($times[$i], $times[$i+1]); // khoảng cách giữa i và i+1
        if ($gap <= MERGE_GAP_MINUTES) $left = $i;
        else break;
    }
    $right = $idxTarget;
    for ($i = $idxTarget + 1; $i < count($all); $i++) {
        $gap = minutes_diff($times[$i-1], $times[$i]); // khoảng cách giữa i-1 và i
        if ($gap <= MERGE_GAP_MINUTES) $right = $i;
        else break;
    }

    $groupIds = [];
    $groupStart = $all[$left]['time'];
    $groupEnd   = $all[$right]['time'];
    for ($i = $left; $i <= $right; $i++) {
        $groupIds[] = intval($all[$i]['id']);
    }
    if (empty($groupIds)) {
        json_out(500, ['ok'=>false, 'error'=>'Không xác định được nhóm để cập nhật.']);
    }

    // 5) Cập nhật trạng thái cho toàn bộ nhóm
    $pdo->beginTransaction();

    $placeholders = implode(',', array_fill(0, count($groupIds), '?'));
    if ($action === 'undo') {
        // HỦY lỗi
        $sql = "UPDATE logs SET isSuccess = 1, reason = NULL WHERE id IN ($placeholders)";
        $params = $groupIds;
    } else {
        // ĐÁNH lỗi
        $sql = "UPDATE logs SET isSuccess = 0, reason = ? WHERE id IN ($placeholders)";
        $params = array_merge([$reason], $groupIds);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $affected = $stmt->rowCount();

    $pdo->commit();

    json_out(200, [
        'ok' => true,
        'action' => $action,
        'updated_count' => $affected,
        'updated_ids' => $groupIds,
        'group_info' => [
            'uuid' => $uuid,
            'serial' => $serial,
            'from_time' => $groupStart,
            'to_time'   => $groupEnd,
            'gap_rule_minutes' => MERGE_GAP_MINUTES
        ]
    ]);

} catch (Exception $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_out(500, ['ok'=>false, 'error'=>'Lỗi máy chủ: '.$e->getMessage()]);
}
