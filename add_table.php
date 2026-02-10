<?php

declare(strict_types=1);

$db = new PDO('sqlite:' . __DIR__ . '/database/app.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ensure foreign keys are enforced
$db->exec('PRAGMA foreign_keys = ON');

// Create lucky spin tables (if not exists)
$db->exec("CREATE TABLE IF NOT EXISTS lucky_spin_prizes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  type TEXT NOT NULL,
  value TEXT,
  probability INTEGER DEFAULT 0,
  quantity INTEGER DEFAULT 0,
  is_active INTEGER DEFAULT 1,
  image TEXT
)");

$db->exec("CREATE TABLE IF NOT EXISTS lucky_spin_players (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  identifier TEXT UNIQUE,
  total_turns INTEGER DEFAULT 0,
  used_turns INTEGER DEFAULT 0,
  last_spin_time TEXT,
  created_at TEXT DEFAULT (datetime('now'))
)");

$db->exec("CREATE TABLE IF NOT EXISTS lucky_spin_history (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  player_id INTEGER,
  prize_id INTEGER,
  prize_snapshot TEXT,
  created_at TEXT DEFAULT (datetime('now')),
  FOREIGN KEY (player_id) REFERENCES lucky_spin_players(id),
  FOREIGN KEY (prize_id) REFERENCES lucky_spin_prizes(id)
)");

// $db->exec("CREATE TABLE IF NOT EXISTS ranks (
//   id INTEGER PRIMARY KEY AUTOINCREMENT,
//   [type] TEXT NOT NULL
// )");

// $db->exec("INSERT INTO ranks ([type]) 
// VALUES 
// ('Sắt'),
// ('Đồng'),
// ('Bạc'),
// ('Vàng'),
// ('Bạch Kim'),
// ('Kim Cương'),
// ('Siêu Việt'),
// ('Bất Tử'),
// ('Tỏa sáng')");

// $db->exec("CREATE TABLE IF NOT EXISTS `status` (
//   id INTEGER PRIMARY KEY AUTOINCREMENT,
//   [type] TEXT NOT NULL
// )");

// $db->exec("INSERT INTO `status` ([type]) 
// VALUES 
// ('Rảnh'),
// ('Bận')");

$db->exec("CREATE TABLE IF NOT EXISTS account_avatars (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  avatar TEXT,
  account_id INTEGER NOT NULL,
  FOREIGN KEY (account_id) REFERENCES game_accounts(id)
)");

$accounts = [
  ['account_67_1751351953_2ad45fdec192fe25.jpg', 46],
  ['account_67_1751351953_2ad45fdec192fe25.jpg', 47],
  ['account_76_1751391209_003daaca539fa811.jpg', 48],
  ['account_76_1751391209_003daaca539fa811.jpg', 49],
];

$stmt = $db->prepare("INSERT INTO account_avatars (avatar, account_id) VALUES (?, ?)");
foreach ($accounts as $acc) {
  $stmt->execute($acc);
}
