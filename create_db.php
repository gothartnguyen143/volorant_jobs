<?php

declare(strict_types=1);

$db = new PDO('sqlite:' . __DIR__ . '/database/app.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// $db->exec("CREATE TABLE IF NOT EXISTS rules (
//   id INTEGER PRIMARY KEY AUTOINCREMENT,
//   rent_acc_rules TEXT NOT NULL,
//   updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
// )");
// $stmt = $db->prepare("INSERT INTO rules (rent_acc_rules) VALUES (?)");
// $stmt->execute(['- Không được rao bán Acc này ra ngoài, nếu có bất cứ hành vi nào sẽ bị cấm thuê vĩnh viễn!\n- Nghiêm cấm hành vi AFK, HACK, CHEAT, BOT, ... Nếu phát hiện sẽ bị cấm thuê vĩnh viễn!']);

$db->exec("CREATE TABLE IF NOT EXISTS sale_accounts (
   id INTEGER PRIMARY KEY AUTOINCREMENT,
    rent_acc_rules TEXT NOT NULL,
    commitment TEXT NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// $password = password_hash('123456', PASSWORD_DEFAULT);
// $stmt = $db->prepare("INSERT INTO users (username, [password], full_name, phone, [role]) VALUES (?, ?, ?, ?, ?)");
// $stmt->execute(['admin', $password, 'Dương Anh Tuấn', '0987654321', 'ADMIN']);

// $accounts = [
//   ['00Valorantime#00Pro', 'gold 1', 'Mã 000', 'RENTED', ''],
//   ['02Valorantime#02Pro', 'gold 3', 'Mã 002', 'RENTED', ''],
//   ['SC ZZZ#8002', 'gold 3', 'Mã 003', 'AVAILABLE', ''],
//   ['04Valorantime#04Pro', 'plat 1', 'Mã 004', 'RENTED', ''],
//   ['Công An Hà Nội#8386', 'gold 3', 'Mã 006', 'RENTED', ''],
//   ['07Valorantime#07Pro', 'sil 1', 'Mã 007', 'AVAILABLE', ''],
//   ['08Valorantime#08Pro', 'plat 2', 'Mã 008', 'RENTED', ''],
//   ['09Valorantime#09Pro', 'plat 1', 'Mã 009', 'AVAILABLE', ''],
//   ['10Valorantime#10Pro', 'plat 2', 'Mã 010', 'RENTED', ''],
//   ['11Valorantime#11Pro', 'plat 3', 'Mã 011', 'AVAILABLE', ''],
//   ['12Valorantime#12Pro', 'plat 1', 'Mã 012', 'RENTED', ''],
//   ['13Valorantime#13Pro', 'sil 2', 'Mã 013', 'RENTED', ''],
//   ['14Valorantime#14Pro', 'gold 2', 'Mã 014', 'RENTED', ''],
//   ['16Valorantime#16Pro', 'gold 2', 'Mã 016', 'AVAILABLE', ''],
//   ['17Valorantime#17Pro', 'plat 1', 'Mã 017', 'RENTED', ''],
//   ['18Valorantime#18Pro', 'gold 3', 'Mã 018', 'RENTED', ''],
//   ['19Valorantime#19Pro', 'plat 2', 'Mã 019', 'RENTED', ''],
//   ['20Valorantime#20Pro', 'gold 3', 'Mã 020', 'AVAILABLE', ''],
//   ['21Valorantime#21Pro', 'plat 1', 'Mã 021', 'RENTED', ''],
//   ['22Valorantime#22Pro', 'plat 1', 'Mã 022', 'RENTED', ''],
//   ['24Valorantime#24Pro', 'plat 1', 'Mã 024', 'AVAILABLE', ''],
//   ['crusch#0512', 'kc 2', 'Mã 025', 'AVAILABLE', ''],
// ];

// $stmt = $db->prepare("INSERT INTO game_accounts (acc_name, rank, game_code, [status], [description]) VALUES (?, ?, ?, ?, ?)");
// foreach ($accounts as $acc) {
//   $stmt->execute($acc);
// }

echo ">>> Database created.";
