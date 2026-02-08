<?php
// ---------- Config ----------
const DB_PATH = 'data.db';

// ---------- Helpers ----------
function db(): PDO {
    $pdo = new PDO('sqlite:' . __DIR__ . '/data.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Bật ràng buộc khóa ngoại
    $pdo->exec('PRAGMA foreign_keys = ON;');
    // Tăng bảo mật một chút
    $pdo->exec('PRAGMA journal_mode = WAL;');
    return $pdo;
}
function json_ok($data = [], int $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}
function json_err($msg, int $code = 400, $extra = null) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    $payload = ['ok' => false, 'error' => (string)$msg];
    if ($extra !== null) $payload['extra'] = $extra;
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}
function post($key, $default = null) {
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

// ---------- Router ----------
$action = $_GET['action'] ?? $_POST['action'] ?? null;
if (!$action) json_err('Missing action', 400);

try {
    $pdo = db();

    switch ($action) {
        // CREATE
        case 'create':
            // yêu cầu: id (FK đến logs.id), uuid, label; note optional
            $id    = (int) post('id', 0);
            $uuid  = trim((string) post('uuid', ''));
            $label = trim((string) post('label', ''));
            $note  = (string) post('note', '');

            if ($id <= 0) json_err('id must be positive integer');
            if ($uuid === '') json_err('uuid is required');
            if ($label === '') json_err('label is required');

            // đảm bảo logs tồn tại
            $stmt = $pdo->prepare('SELECT 1 FROM logs WHERE id=? LIMIT 1');
            $stmt->execute([$id]);
            if (!$stmt->fetch()) json_err('logs.id not found', 404);

            // chèn
            $stmt = $pdo->prepare('INSERT INTO Identification (id, uuid, label, note) VALUES (?, ?, ?, ?)');
            $stmt->execute([$id, $uuid, $label, $note]);

            json_ok(['id' => $id, 'uuid' => $uuid, 'label' => $label, 'note' => $note], 201);
            break;

        // READ ONE (by id) 
        case 'get':
            $id = (int) post('id', 0);
            if ($id <= 0) json_err('id must be positive integer');

            $stmt = $pdo->prepare('SELECT id, uuid, label, note FROM Identification WHERE id=?');
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) json_err('Identification not found', 404);

            json_ok($row);
            break;

        // LIST (filter + pagination)
        case 'list':
            $uuid  = trim((string) post('uuid', ''));     // lọc chính xác theo uuid (optional)
            $q     = trim((string) post('q', ''));        // tìm kiếm label/note (LIKE)
            $limit = max(1, min(100, (int) post('limit', 20)));
            $offset= max(0, (int) post('offset', 0));

            $where = [];
            $params = [];

            if ($uuid !== '') {
                $where[] = 'uuid = ?';
                $params[] = $uuid;
            }
            if ($q !== '') {
                $where[] = '(label LIKE ? OR note LIKE ?)';
                $params[] = "%$q%";
                $params[] = "%$q%";
            }

            $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

            // total
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Identification $whereSql");
            $stmt->execute($params);
            $total = (int) $stmt->fetchColumn();

            // data
            $stmt = $pdo->prepare("SELECT id, uuid, label, note 
                                   FROM Identification 
                                   $whereSql 
                                   ORDER BY id DESC 
                                   LIMIT ? OFFSET ?");
            $params2 = array_merge($params, [$limit, $offset]);
            $stmt->execute($params2);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            json_ok(['total' => $total, 'limit' => $limit, 'offset' => $offset, 'items' => $rows]);
            break;

        // UPDATE (partial)
        case 'update':
            $id = (int) post('id', 0);
            if ($id <= 0) json_err('id must be positive integer');

            // các trường tùy chọn
            $uuid  = post('uuid', null);
            $label = post('label', null);
            $note  = post('note', null);

            $sets = [];
            $params = [];

            if ($uuid !== null)  { $sets[] = 'uuid = ?';  $params[] = trim((string)$uuid); }
            if ($label !== null) { $sets[] = 'label = ?'; $params[] = trim((string)$label); }
            if ($note !== null)  { $sets[] = 'note = ?';  $params[] = (string)$note; }

            if (!$sets) json_err('No fields to update');

            // đảm bảo tồn tại
            $stmt = $pdo->prepare('SELECT 1 FROM Identification WHERE id=? LIMIT 1');
            $stmt->execute([$id]);
            if (!$stmt->fetch()) json_err('Identification not found', 404);

            $sql = 'UPDATE Identification SET ' . implode(', ', $sets) . ' WHERE id = ?';
            $params[] = $id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            json_ok(['id' => $id, 'affected' => $stmt->rowCount()]);
            break;

        // DELETE
        case 'delete':
            $id = (int) post('id', 0);
            if ($id <= 0) json_err('id must be positive integer');

            $stmt = $pdo->prepare('DELETE FROM Identification WHERE id=?');
            $stmt->execute([$id]);

            json_ok(['deleted_id' => $id, 'affected' => $stmt->rowCount()]);
            break;

        default:
            json_err('Unknown action', 400);
    }

} catch (PDOException $e) {
    json_err('DB error', 500, $e->getMessage());
} catch (Throwable $e) {
    json_err('Server error', 500, $e->getMessage());
}
