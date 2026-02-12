<?php
// Script to add new table 'computer_requirements' and modify 'game_accounts' table
// Run this script in your project root or via PHP CLI to update the database.

$dbPath = __DIR__ . '/database/app.sqlite';

try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('PRAGMA foreign_keys = ON');

    // // Create the new table 'computer_requirements' if it doesn't exist
    // $db->exec("
    //     CREATE TABLE IF NOT EXISTS computer_requirements (
    //         id INTEGER PRIMARY KEY AUTOINCREMENT,
    //         name TEXT NOT NULL
    //     );
    // ");
    // echo "Table 'computer_requirements' created or already exists.\n";

    // // Seed sample data if table is empty
    // $count = $db->query('SELECT COUNT(*) FROM computer_requirements')->fetchColumn();
    // if ($count == 0) {
    //     $sampleData = [
    //         'Iommu',
    //         'Hvci',
    //         'Secure boot',
    //         'Tpm 2.0'
    //     ];
    //     $stmt = $db->prepare('INSERT INTO computer_requirements (name) VALUES (?)');
    //     foreach ($sampleData as $name) {
    //         $stmt->execute([$name]);
    //     }
    //     echo "Inserted 4 sample records into 'computer_requirements'.\n";
    // } else {
    //     echo "'computer_requirements' already has data ($count records). Skipping seeding.\n";
    // }

    // Create the junction table 'account_requirement'
    $db->exec("
        CREATE TABLE IF NOT EXISTS account_requirement (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            id_game_accounts INTEGER NOT NULL,
            id_cp_requirement INTEGER NOT NULL,
            created_at TEXT DEFAULT (datetime('now')),
            FOREIGN KEY (id_game_accounts) REFERENCES game_accounts(id),
            FOREIGN KEY (id_cp_requirement) REFERENCES computer_requirements(id)
        );
    ");
    echo "Table 'account_requirement' created or already exists.\n";

    // // Check if 'cp_require_id' column exists in 'game_accounts'
    // $stmt = $db->query("PRAGMA table_info(game_accounts)");
    // $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // $columnExists = false;
    // foreach ($columns as $col) {
    //     if ($col['name'] === 'cp_require_id') {
    //         $columnExists = true;
    //         break;
    //     }
    // }

    // if ($columnExists) {
    //     // Drop the column
    //     $db->exec("ALTER TABLE game_accounts DROP COLUMN cp_require_id;");
    //     echo "Column 'cp_require_id' dropped from 'game_accounts'.\n";
    // } else {
    //     echo "Column 'cp_require_id' does not exist in 'game_accounts'.\n";
    // }

    echo "Database update completed successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>