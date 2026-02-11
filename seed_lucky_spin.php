<?php
// Seeder for lucky spin sample data
$dbFile = __DIR__ . '/app.sqlite';
if (!file_exists($dbFile)) {
    echo "Database file not found: $dbFile\n";
    exit(1);
}

try {
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ensure foreign keys on
    $db->exec('PRAGMA foreign_keys = ON');

    // Seed prizes if none exist
    $cnt = (int) $db->query('SELECT COUNT(*) FROM lucky_spin_prizes')->fetchColumn();
    if ($cnt === 0) {
        $prizes = [
            ['name' => 'Tiền mặt 10.000', 'type' => 'REAL', 'value' => '10000', 'probability' => 30, 'quantity' => -1, 'is_active' => 1, 'image' => ''],
            ['name' => 'Mã giảm giá 50%', 'type' => 'CODE', 'value' => 'GIFT50', 'probability' => 10, 'quantity' => 100, 'is_active' => 1, 'image' => ''],
            ['name' => 'Quay miễn phí', 'type' => 'TEXT', 'value' => 'Free Spin', 'probability' => 60, 'quantity' => -1, 'is_active' => 1, 'image' => '']
        ];

        $stmt = $db->prepare('INSERT INTO lucky_spin_prizes (name, type, value, probability, quantity, is_active, image) VALUES (:name, :type, :value, :probability, :quantity, :is_active, :image)');
        $db->beginTransaction();
        foreach ($prizes as $p) {
            $stmt->execute([
                ':name' => $p['name'],
                ':type' => $p['type'],
                ':value' => $p['value'],
                ':probability' => $p['probability'],
                ':quantity' => $p['quantity'],
                ':is_active' => $p['is_active'],
                ':image' => $p['image']
            ]);
        }
        $db->commit();
        echo "Inserted " . count($prizes) . " prizes.\n";
    } else {
        echo "Prizes already exist ($cnt). Skipping prize seeding.\n";
    }

    // Seed a sample player
    $cntPlayers = (int) $db->query('SELECT COUNT(*) FROM lucky_spin_players')->fetchColumn();
    if ($cntPlayers === 0) {
        $stmt = $db->prepare('INSERT INTO lucky_spin_players (identifier, total_turns, used_turns, last_spin_time) VALUES (:identifier, :total_turns, :used_turns, :last_spin_time)');
        $stmt->execute([
            ':identifier' => 'seed_player_1',
            ':total_turns' => 10,
            ':used_turns' => 0,
            ':last_spin_time' => null
        ]);
        echo "Inserted sample player.\n";
    } else {
        echo "Players already exist ($cntPlayers). Skipping player seeding.\n";
    }

    // Insert one history row if none exist
    $cntHist = (int) $db->query('SELECT COUNT(*) FROM lucky_spin_history')->fetchColumn();
    if ($cntHist === 0) {
        // get a player id and a prize id
        $playerId = (int) $db->query("SELECT id FROM lucky_spin_players ORDER BY id ASC LIMIT 1")->fetchColumn();
        $prizeId = (int) $db->query("SELECT id FROM lucky_spin_prizes ORDER BY id ASC LIMIT 1")->fetchColumn();
        if ($playerId && $prizeId) {
            $snapshot = json_encode(['prize_id' => $prizeId, 'note' => 'Seed entry']);
            $stmt = $db->prepare('INSERT INTO lucky_spin_history (player_id, prize_id, prize_snapshot) VALUES (:player_id, :prize_id, :prize_snapshot)');
            $stmt->execute([
                ':player_id' => $playerId,
                ':prize_id' => $prizeId,
                ':prize_snapshot' => $snapshot
            ]);
            echo "Inserted one history row.\n";
        } else {
            echo "Cannot insert history: missing player or prize.\n";
        }
    } else {
        echo "History already exists ($cntHist). Skipping history seeding.\n";
    }

    // Summary counts
    $p = (int) $db->query('SELECT COUNT(*) FROM lucky_spin_prizes')->fetchColumn();
    $pl = (int) $db->query('SELECT COUNT(*) FROM lucky_spin_players')->fetchColumn();
    $h = (int) $db->query('SELECT COUNT(*) FROM lucky_spin_history')->fetchColumn();
    echo "Summary: prizes={$p}, players={$pl}, history={$h}\n";

} catch (Exception $e) {
    echo "Seeder error: " . $e->getMessage() . "\n";
    exit(1);
}

return 0;
