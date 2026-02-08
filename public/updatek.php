<?php
// migrate_finance.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$dbFile = __DIR__ . '/data.db';

try {
  if (!file_exists($dbFile)) { touch($dbFile); }

  $pdo = new PDO('sqlite:' . $dbFile);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->exec('PRAGMA foreign_keys = ON');

  $pdo->beginTransaction();

  // 1) BẢNG RÚT TIỀN THEO EMAIL
  $pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS withdrawals (
  id           INTEGER PRIMARY KEY AUTOINCREMENT,
  email        TEXT NOT NULL,
  amount_usd   REAL NOT NULL CHECK (amount_usd >= 0),
  created_at   TEXT NOT NULL DEFAULT (datetime('now','localtime')),
  note         TEXT
);
SQL);
  $pdo->exec("CREATE INDEX IF NOT EXISTS idx_withdrawals_email ON withdrawals(email)");
  $pdo->exec("CREATE INDEX IF NOT EXISTS idx_withdrawals_created_at ON withdrawals(created_at)");

  // 2) BẢNG VỐN THEO UID
  $pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS capitals (
  id           INTEGER PRIMARY KEY AUTOINCREMENT,
  uid          TEXT NOT NULL,
  vnd_amount   INTEGER NOT NULL CHECK (vnd_amount >= 0),
  usd_rate     REAL NOT NULL CHECK (usd_rate > 0), -- ví dụ 25000 = 25k VND / $1
  usd_amount   REAL NOT NULL,                      -- = vnd_amount / usd_rate, lưu cứng theo thời điểm nhập
  source       TEXT,
  created_at   TEXT NOT NULL DEFAULT (datetime('now','localtime')),
  note         TEXT
);
SQL);
  $pdo->exec("CREATE INDEX IF NOT EXISTS idx_capitals_uid ON capitals(uid)");
  $pdo->exec("CREATE INDEX IF NOT EXISTS idx_capitals_created_at ON capitals(created_at)");

  // 3) VIEW THỐNG KÊ RÚT TIỀN THEO EMAIL
  $pdo->exec("DROP VIEW IF EXISTS view_withdrawals_by_email");
  $pdo->exec(<<<SQL
CREATE VIEW view_withdrawals_by_email AS
SELECT
  email,
  COUNT(*)                         AS withdraw_count,
  ROUND(SUM(amount_usd), 2)        AS total_withdraw_usd,
  MIN(created_at)                  AS first_withdraw_at,
  MAX(created_at)                  AS last_withdraw_at
FROM withdrawals
GROUP BY email;
SQL);

  // 4) VIEW TỔNG RÚT TIỀN
  $pdo->exec("DROP VIEW IF EXISTS view_withdrawals_total");
  $pdo->exec(<<<SQL
CREATE VIEW view_withdrawals_total AS
SELECT ROUND(COALESCE(SUM(amount_usd),0), 2) AS total_withdraw_usd FROM withdrawals;
SQL);

  // 5) VIEW THỐNG KÊ VỐN THEO UID
  $pdo->exec("DROP VIEW IF EXISTS view_capitals_by_uid");
  $pdo->exec(<<<SQL
CREATE VIEW view_capitals_by_uid AS
SELECT
  uid,
  SUM(vnd_amount)             AS total_capital_vnd,
  ROUND(SUM(usd_amount), 2)   AS total_capital_usd,
  COUNT(*)                    AS capital_count,
  MIN(created_at)             AS first_capital_at,
  MAX(created_at)             AS last_capital_at
FROM capitals
GROUP BY uid;
SQL);

  // 6) VIEW TỔNG VỐN
  $pdo->exec("DROP VIEW IF EXISTS view_capitals_total");
  $pdo->exec(<<<SQL
CREATE VIEW view_capitals_total AS
SELECT
  COALESCE(SUM(vnd_amount),0)         AS total_capital_vnd,
  ROUND(COALESCE(SUM(usd_amount),0),2) AS total_capital_usd
FROM capitals;
SQL);

  $pdo->commit();
  echo "OK: Migrated\n";

} catch (Throwable $e) {
  if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
  http_response_code(500);
  echo "ERROR: " . $e->getMessage();
}
