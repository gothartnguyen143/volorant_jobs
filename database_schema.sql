-- Database Schema Dump
-- Generated from project files
-- Date: February 12, 2026

PRAGMA foreign_keys = ON;

-- Table: lucky_spin_prizes
CREATE TABLE IF NOT EXISTS lucky_spin_prizes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  probability INTEGER DEFAULT 0,
  quantity INTEGER DEFAULT 0,
  is_active INTEGER DEFAULT 1
);

-- Table: lucky_spin_players
CREATE TABLE IF NOT EXISTS lucky_spin_players (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  identifier TEXT UNIQUE,
  total_turns INTEGER DEFAULT 0,
  used_turns INTEGER DEFAULT 0,
  last_spin_time TEXT,
  created_at TEXT DEFAULT (datetime('now'))
);

-- Table: lucky_spin_history
CREATE TABLE IF NOT EXISTS lucky_spin_history (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  player_id INTEGER,
  prize_id INTEGER,
  prize_snapshot TEXT,
  created_at TEXT DEFAULT (datetime('now')),
  FOREIGN KEY (player_id) REFERENCES lucky_spin_players(id),
  FOREIGN KEY (prize_id) REFERENCES lucky_spin_prizes(id)
);

-- Table: game_accounts
CREATE TABLE IF NOT EXISTS game_accounts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  acc_name TEXT NOT NULL,
  rank TEXT NOT NULL,
  game_code TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'Rảnh' CHECK (status IN ('Rảnh', 'Bận', 'Check')),
  description TEXT,
  device_type TEXT NOT NULL CHECK (device_type IN ('Máy nhà', 'Tất cả')),
  avatar TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  rent_to_time DATETIME,
  rent_from_time DATETIME
);

-- Table: account_avatars
CREATE TABLE IF NOT EXISTS account_avatars (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  avatar TEXT,
  account_id INTEGER NOT NULL,
  FOREIGN KEY (account_id) REFERENCES game_accounts(id)
);

-- Table: sale_accounts
CREATE TABLE IF NOT EXISTS sale_accounts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  rent_acc_rules TEXT NOT NULL,
  commitment TEXT NOT NULL,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table: account_requirement
CREATE TABLE IF NOT EXISTS account_requirement (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  id_game_accounts INTEGER NOT NULL,
  id_cp_requirement INTEGER NOT NULL,
  created_at TEXT DEFAULT (datetime('now')),
  FOREIGN KEY (id_game_accounts) REFERENCES game_accounts(id),
  FOREIGN KEY (id_cp_requirement) REFERENCES computer_requirements(id)
);

-- Table: withdrawals
CREATE TABLE IF NOT EXISTS withdrawals (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  email TEXT NOT NULL,
  amount_usd REAL NOT NULL CHECK (amount_usd >= 0),
  created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
  note TEXT
);
CREATE INDEX IF NOT EXISTS idx_withdrawals_email ON withdrawals(email);
CREATE INDEX IF NOT EXISTS idx_withdrawals_created_at ON withdrawals(created_at);

-- Table: capitals
CREATE TABLE IF NOT EXISTS capitals (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  uid TEXT NOT NULL,
  vnd_amount INTEGER NOT NULL CHECK (vnd_amount >= 0),
  usd_rate REAL NOT NULL CHECK (usd_rate > 0),
  usd_amount REAL NOT NULL,
  source TEXT,
  created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
  note TEXT
);
CREATE INDEX IF NOT EXISTS idx_capitals_uid ON capitals(uid);
CREATE INDEX IF NOT EXISTS idx_capitals_created_at ON capitals(created_at);

-- Table: logs
CREATE TABLE IF NOT EXISTS logs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  uuid TEXT NOT NULL,
  email TEXT NOT NULL,
  spaces INTEGER NOT NULL,
  amount REAL NOT NULL,
  time TEXT NOT NULL,
  serial TEXT NOT NULL
);

-- Table: Identification
CREATE TABLE IF NOT EXISTS Identification (
  id INTEGER,
  uuid TEXT,
  label TEXT,
  note TEXT,
  FOREIGN KEY (id) REFERENCES logs(id)
);

-- Views
DROP VIEW IF EXISTS view_withdrawals_by_email;
CREATE VIEW view_withdrawals_by_email AS
SELECT
  email,
  COUNT(*) AS withdraw_count,
  ROUND(SUM(amount_usd), 2) AS total_withdraw_usd,
  MIN(created_at) AS first_withdraw_at,
  MAX(created_at) AS last_withdraw_at
FROM withdrawals
GROUP BY email;

DROP VIEW IF EXISTS view_withdrawals_total;
CREATE VIEW view_withdrawals_total AS
SELECT ROUND(COALESCE(SUM(amount_usd),0), 2) AS total_withdraw_usd FROM withdrawals;

DROP VIEW IF EXISTS view_capitals_by_uid;
CREATE VIEW view_capitals_by_uid AS
SELECT
  uid,
  SUM(vnd_amount) AS total_capital_vnd,
  ROUND(SUM(usd_amount), 2) AS total_capital_usd,
  COUNT(*) AS capital_count,
  MIN(created_at) AS first_capital_at,
  MAX(created_at) AS last_capital_at
FROM capitals
GROUP BY uid;

DROP VIEW IF EXISTS view_capitals_total;
CREATE VIEW view_capitals_total AS
SELECT
  COALESCE(SUM(vnd_amount),0) AS total_capital_vnd,
  ROUND(COALESCE(SUM(usd_amount),0),2) AS total_capital_usd
FROM capitals;