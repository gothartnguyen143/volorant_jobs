<?php
// Script to recreate lucky_spin tables with updated schema

$dbPath = __DIR__ . '/app.sqlite';

// if (file_exists($dbPath)) {
//     unlink($dbPath); // Delete old DB
// }

$db = new PDO('sqlite:' . $dbPath);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("PRAGMA foreign_keys = ON;");

$db->exec("
CREATE TABLE IF NOT EXISTS lucky_spin_prizes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  probability INTEGER DEFAULT 0,
  quantity INTEGER DEFAULT 0,
  is_active INTEGER DEFAULT 1
);
");

$db->exec("
CREATE TABLE IF NOT EXISTS lucky_spin_players (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  identifier TEXT UNIQUE,
  total_turns INTEGER DEFAULT 0,
  used_turns INTEGER DEFAULT 0,
  last_spin_time TEXT,
  created_at TEXT DEFAULT (datetime('now'))
);
");

$db->exec("
CREATE TABLE IF NOT EXISTS lucky_spin_history (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  player_id INTEGER,
  prize_id INTEGER,
  prize_snapshot TEXT,
  created_at TEXT DEFAULT (datetime('now')),
  FOREIGN KEY (player_id) REFERENCES lucky_spin_players(id),
  FOREIGN KEY (prize_id) REFERENCES lucky_spin_prizes(id)
);
");

echo "Database recreated successfully.\n";
?>