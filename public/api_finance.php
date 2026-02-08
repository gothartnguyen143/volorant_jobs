<?php
/**
 * api_finance.php (ALL-IN-ONE)
 *
 * Actions:
 *  - POST ?action=withdrawal.create    (password, email, amount_usd, note?)
 *  - POST ?action=capital.create       (password, uid, vnd_amount, usd_rate, source?, note?)
 *  - GET  ?action=stats[&email=&from=&to=]
 *  - GET  ?action=withdrawals.list[&email=&from=&to=&limit=50]
 *  - GET  ?action=capitals.list[&uid=&from=&to=&limit=50]
 *  - POST ?action=withdrawal.delete    (password=kiemtienthoi321, id)
 *  - POST ?action=capital.delete       (password=kiemtienthoi321, id)
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

// ---------------- CONFIG ----------------
const FINANCE_PASSWORD        = 'kiemtienthoi1234'; // mật khẩu tạo/sửa (create)
const FINANCE_DELETE_PASSWORD = 'ncnksk12e31a!!!!!';  // mật khẩu xóa (delete)

// --------------- HELPERS ----------------
function custom_md5($input) {
    // Chuỗi prefix và suffix
    $prefix = "natproo!!@@@";
    $suffix = "bchshsj!@@!!@";

    // Ghép chuỗi
    $combined = $prefix . $input . $suffix;

    // Hash md5 và trả về
    return md5($combined);
}
function db(): PDO {
  static $pdo = null;
  if ($pdo) return $pdo;
  $pdo = new PDO('sqlite:' . __DIR__ . '/data.db');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->exec('PRAGMA foreign_keys = ON');
  return $pdo;
}
function json_ok($data = []) {
  echo json_encode(['ok'=>true] + $data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}
function json_err($msg, $code=400) {
  http_response_code(is_int($code)?$code:400);
  echo json_encode(['ok'=>false,'error'=>$msg], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}
function need_pass_or_die(?string $pass) {
  if ($pass !== FINANCE_PASSWORD) json_err('Sai mật khẩu', 401);
}
function method($m){ return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === strtoupper($m); }
function post($k, $d=null){ return $_POST[$k] ?? $d; }
function getv($k, $d=null){ return $_GET[$k]  ?? $d; }

/** Parse "YYYY-MM-DD" hoặc "YYYY-MM-DD HH:MM:SS" -> DateTime|null */
function parse_dt_or_null($s) {
  $s = trim((string)$s);
  if ($s==='') return null;
  $fmt = (strlen($s)===10) ? 'Y-m-d' : 'Y-m-d H:i:s';
  $dt = DateTime::createFromFormat($fmt, $s);
  return ($dt && $dt->format($fmt)===$s) ? $dt : null;
}

// --------------- ROUTER -----------------
$action = (string) getv('action', '');

try {
  switch ($action) {

    // ----------------- CREATE WITHDRAWAL ----------------- Thêm rút tiền
    case 'withdrawal.create':
      if (!method('POST')) json_err('Method not allowed', 405);

      $pass   = post('password');
      $email  = trim((string)post('email',''));
      $amount = (float)post('amount_usd', -1);
      $note   = trim((string)post('note',''));

    //   need_pass_or_die($pass);
    if ($pass !== FINANCE_DELETE_PASSWORD) json_err('Sai mật khẩu xóa', 401);
      if ($email==='') json_err('Email không được trống');
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) json_err('Email không hợp lệ');
      if (!is_finite($amount) || $amount < 0) json_err('Số tiền (USD) không hợp lệ');

      $pdo = db();
      $stmt = $pdo->prepare("INSERT INTO withdrawals(email, amount_usd, note) VALUES (?,?,?)");
      $stmt->execute([$email, $amount, $note ?: null]);

      json_ok(['id'=>(int)$pdo->lastInsertId()]);
      break;

    // ----------------- CREATE CAPITAL ----------------- Thêm vốn
    case 'capital.create':
      if (!method('POST')) json_err('Method not allowed', 405);

      $pass      = post('password');
      $uid       = trim((string)post('uid',''));
      $vnd_raw   = post('vnd_amount', '-1'); // yêu cầu số nguyên
      $usd_rate  = (float)post('usd_rate', 25000);
      $source    = trim((string)post('source',''));
      $note      = trim((string)post('note',''));

    //   need_pass_or_die($pass);
    if ($pass !== custom_md5($uid)) json_err('Sai mật khẩu rồi phờ ren', 401);
      
      if ($uid==='') json_err('UID không được trống');

      // VND phải là số nguyên không âm
      if (!is_numeric($vnd_raw) || (string)(int)$vnd_raw !== (string)$vnd_raw) json_err('Số tiền VND phải là số nguyên');
      $vnd = (int)$vnd_raw;
    //   if ($vnd < 0) json_err('Số tiền VND không hợp lệ');

      if (!is_finite($usd_rate) || $usd_rate <= 0) json_err('Tỷ giá không hợp lệ');

      $usd = $vnd / $usd_rate;

      $pdo = db();
      $stmt = $pdo->prepare("INSERT INTO capitals_nat_pro(uid, vnd_amount, usd_rate, usd_amount, source, note) VALUES (?,?,?,?,?,?)");
      $stmt->execute([$uid, $vnd, $usd_rate, $usd, ($source?:null), ($note?:null)]);

      json_ok([
        'id' => (int)$pdo->lastInsertId(),
        'usd_amount' => round($usd, 4)
      ]);
      break;

    // ----------------- STATS (with filters) -----------------
    case 'stats':
  if (!method('GET')) json_err('Method not allowed', 405);

  $pdo = db();
  $emailFilter = trim((string)getv('email',''));
  $uidFilter   = trim((string)getv('uid',''));
  $from = parse_dt_or_null(getv('from',''));
  $to   = parse_dt_or_null(getv('to',''));

  // --- Chuẩn bị điều kiện lọc cho withdrawals ---
  // Ưu tiên: email > uid > all
  $where  = " WHERE 1=1";
  $args   = [];

  if ($emailFilter !== '') {
    $where .= " AND w.email = ?";
    $args[] = $emailFilter;
  } elseif ($uidFilter !== '') {
    // Lọc theo UID: duyệt bảng logs để lấy các email thuộc uuid này
    // Dùng subquery để tránh phải build IN(...)
    $where .= " AND w.email IN (SELECT DISTINCT l.email FROM logs l WHERE l.uuid = ?)";
    $args[] = $uidFilter;
  }

  if ($from){ $where .= " AND datetime(w.created_at) >= datetime(?)"; $args[] = $from->format('Y-m-d H:i:s'); }
  if ($to){   $where .= " AND datetime(w.created_at) <= datetime(?)"; $args[] = $to->format('Y-m-d H:i:s'); }

  // --- Tổng đã rút (theo bộ lọc trên) ---
  $sqlTotal = "SELECT ROUND(COALESCE(SUM(w.amount_usd),0),2)
               FROM withdrawals w" . $where;

  $stmt = $pdo->prepare($sqlTotal);
  $stmt->execute($args);
  $totalWithdraw = (float)($stmt->fetchColumn() ?: 0);

  // --- Bảng rút tiền theo email (cũng theo cùng bộ lọc) ---
  $sqlByEmail = "SELECT w.email,
                        COUNT(*)                       AS withdraw_count,
                        ROUND(COALESCE(SUM(w.amount_usd),0),2) AS total_withdraw_usd,
                        MIN(w.created_at)             AS first_withdraw_at,
                        MAX(w.created_at)             AS last_withdraw_at
                 FROM withdrawals w" . $where . "
                 GROUP BY w.email
                 ORDER BY total_withdraw_usd DESC";

  $stmt = $pdo->prepare($sqlByEmail);
  $stmt->execute($args);
  $withdrawByEmail = [];
  foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $row['withdraw_count']     = (int)$row['withdraw_count'];
    $row['total_withdraw_usd'] = (float)$row['total_withdraw_usd'];
    $withdrawByEmail[] = $row;
  }

  // --- Tổng vốn & vốn theo UID (giữ nguyên từ view hiện có, không phụ thuộc bộ lọc) ---
  $rowTotalCap = $pdo->query("SELECT total_capital_vnd, total_capital_usd FROM view_capitals_nat_pro_total")->fetch(PDO::FETCH_ASSOC);
  $totalCapitalVnd = (int)($rowTotalCap['total_capital_vnd'] ?? 0);
  $totalCapitalUsd = (float)($rowTotalCap['total_capital_usd'] ?? 0.0);

  $capitalByUid = [];
  foreach ($pdo->query("SELECT uid, total_capital_vnd, total_capital_usd, capital_count, first_capital_at, last_capital_at
                        FROM view_capitals_nat_pro_by_uid
                        ORDER BY total_capital_usd DESC") as $row) {
    $row['total_capital_vnd'] = (int)$row['total_capital_vnd'];
    $row['total_capital_usd'] = (float)$row['total_capital_usd'];
    $row['capital_count']     = (int)$row['capital_count'];
    $capitalByUid[] = $row;
  }

  json_ok([
    'filters' => [
      'email' => $emailFilter ?: null,
      'uid'   => $uidFilter   ?: null,
      'from'  => $from? $from->format('Y-m-d H:i:s') : null,
      'to'    => $to?   $to->format('Y-m-d H:i:s')   : null,
    ],
    'total_withdraw_usd'    => $totalWithdraw,
    'withdrawals_by_email'  => $withdrawByEmail,
    'total_capital_vnd'     => $totalCapitalVnd,
    'total_capital_usd'     => $totalCapitalUsd,
    'capitals_by_uid'       => $capitalByUid
  ]);
  break;

    // ----------------- LIST WITHDRAWALS -----------------
    // GET ?action=withdrawals.list[&email=&from=&to=&limit=50]
    case 'withdrawals.list':
      if (!method('GET')) json_err('Method not allowed', 405);
      $pdo = db();
      $email = trim((string)getv('email',''));
      $from  = parse_dt_or_null(getv('from',''));
      $to    = parse_dt_or_null(getv('to',''));
      $limit = (int)getv('limit', 50); if ($limit<1 || $limit>500) $limit = 50;

      $sql = "SELECT id, email, amount_usd, created_at, note
              FROM withdrawals WHERE 1=1";
      $args = [];
      if ($email!==''){ $sql.=" AND email=?"; $args[]=$email; }
      if ($from){ $sql.=" AND datetime(created_at) >= datetime(?)"; $args[]=$from->format('Y-m-d H:i:s'); }
      if ($to){   $sql.=" AND datetime(created_at) <= datetime(?)"; $args[]=$to->format('Y-m-d H:i:s'); }
      $sql .= " ORDER BY datetime(created_at) DESC LIMIT ".$limit;

      $stmt=$pdo->prepare($sql); $stmt->execute($args);
      json_ok(['rows'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
      break;

    // ----------------- LIST CAPITALS -----------------
    // GET ?action=capitals.list[&uid=&from=&to=&limit=50]
    case 'capitals.list':
      if (!method('GET')) json_err('Method not allowed', 405);
      $pdo = db();
      $uid   = trim((string)getv('uid',''));
      $from  = parse_dt_or_null(getv('from',''));
      $to    = parse_dt_or_null(getv('to',''));
      $limit = (int)getv('limit', 50); if ($limit<1 || $limit>500) $limit = 50;

      $sql = "SELECT id, uid, vnd_amount, usd_rate, usd_amount, source, created_at, note
              FROM capitals_nat_pro WHERE 1=1";
      $args=[];
      if ($uid!==''){ $sql.=" AND uid=?"; $args[]=$uid; }
      if ($from){ $sql.=" AND datetime(created_at) >= datetime(?)"; $args[]=$from->format('Y-m-d H:i:s'); }
      if ($to){   $sql.=" AND datetime(created_at) <= datetime(?)"; $args[]=$to->format('Y-m-d H:i:s'); }
      $sql .= " ORDER BY datetime(created_at) DESC LIMIT ".$limit;

      $stmt=$pdo->prepare($sql); $stmt->execute($args);
      json_ok(['rows'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
      break;

    // ----------------- DELETE WITHDRAWAL ----------------- xóa rút tiền
    // POST ?action=withdrawal.delete (password=kiemtienthoi321, id)
    case 'withdrawal.delete':
      if (!method('POST')) json_err('Method not allowed', 405);
      $pass = post('password');
      $id   = (int)post('id',0);
      if ($pass !== FINANCE_DELETE_PASSWORD) json_err('Sai mật khẩu xóa', 401);
      if ($id<=0) json_err('ID không hợp lệ');

      $pdo=db();
      $stmt=$pdo->prepare("DELETE FROM withdrawals WHERE id=?");
      $stmt->execute([$id]);
      json_ok(['deleted_id'=>$id, 'affected'=>$stmt->rowCount()]);
      break;

    // ----------------- DELETE CAPITAL ----------------- xóa vốn
    // POST ?action=capital.delete (password=kiemtienthoi321, id)
    case 'capital.delete':
      if (!method('POST')) json_err('Method not allowed', 405);
      $pass = post('password');
      $id   = (int)post('id',0);
    //   if ($pass !== FINANCE_DELETE_PASSWORD) json_err('Sai mật khẩu xóa', 401);
    $pdo = db();

    // Lấy uid theo id
    $stmt = $pdo->prepare("SELECT uid FROM capitals_nat_pro WHERE id=? LIMIT 1");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        json_err('Không tìm thấy bản ghi', 404);
    }

    $uid = $row['uid'];
    if ($pass !== custom_md5($uid)) json_err('Sai mật khẩu rồi phờ ren', 401);
      
      if ($id<=0) json_err('ID không hợp lệ');

 
      $stmt=$pdo->prepare("DELETE FROM capitals_nat_pro WHERE id=?");
      $stmt->execute([$id]);
      json_ok(['deleted_id'=>$id, 'affected'=>$stmt->rowCount()]);
      break;

    // ----------------- DEFAULT -----------------
    default:
      json_err('Unknown action', 404);
  }

} catch (Throwable $e) {
  json_err('DB error: '.$e->getMessage(), 500);
}
