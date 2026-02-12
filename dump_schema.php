<?php

// Script to dump database schema
$dbPath = __DIR__ . '/database/app.sqlite';

if (!file_exists($dbPath)) {
    die("Database file not found: $dbPath\n");
}

try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get all table names
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchAll(PDO::FETCH_COLUMN);

    echo "Database Schema for: $dbPath\n";
    echo str_repeat("=", 50) . "\n\n";

    foreach ($tables as $table) {
        echo "Table: $table\n";
        echo str_repeat("-", 20) . "\n";

        // Get table info
        $stmt = $db->query("PRAGMA table_info($table)");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($columns as $col) {
            echo "  - {$col['name']}: {$col['type']}";
            if ($col['pk']) echo " (PRIMARY KEY)";
            if ($col['notnull']) echo " NOT NULL";
            if ($col['dflt_value'] !== null) echo " DEFAULT {$col['dflt_value']}";
            echo "\n";
        }

        // Get foreign keys
        $stmt = $db->query("PRAGMA foreign_key_list($table)");
        $fks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($fks)) {
            echo "  Foreign Keys:\n";
            foreach ($fks as $fk) {
                echo "    - {$fk['from']} -> {$fk['table']}({$fk['to']})\n";
            }
        }

        // Get indexes
        $stmt = $db->query("PRAGMA index_list($table)");
        $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($indexes)) {
            echo "  Indexes:\n";
            foreach ($indexes as $idx) {
                if (!$idx['unique'] && strpos($idx['name'], 'sqlite_autoindex') === false) {
                    echo "    - {$idx['name']}\n";
                }
            }
        }

        echo "\n";
    }

    // Get views
    $views = $db->query("SELECT name FROM sqlite_master WHERE type='view'")->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($views)) {
        echo "Views:\n";
        echo str_repeat("-", 10) . "\n";
        foreach ($views as $view) {
            echo "- $view\n";
        }
        echo "\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>