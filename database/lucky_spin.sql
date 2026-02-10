PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS lucky_spin_prizes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  type TEXT NOT NULL,
  value TEXT,
  probability INTEGER DEFAULT 0,
  quantity INTEGER DEFAULT 0,
  is_active INTEGER DEFAULT 1,
  image TEXT
);

CREATE TABLE IF NOT EXISTS lucky_spin_players (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  identifier TEXT UNIQUE,
  total_turns INTEGER DEFAULT 0,
  used_turns INTEGER DEFAULT 0,
  last_spin_time TEXT,
  created_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS lucky_spin_history (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  player_id INTEGER,
  prize_id INTEGER,
  prize_snapshot TEXT,
  created_at TEXT DEFAULT (datetime('now')),
  FOREIGN KEY (player_id) REFERENCES lucky_spin_players(id),
  FOREIGN KEY (prize_id) REFERENCES lucky_spin_prizes(id)
);
