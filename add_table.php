<?php

declare(strict_types=1);

$db = new PDO('sqlite:' . __DIR__ . '/database/app.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
