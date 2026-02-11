<?php
$db = new PDO('sqlite:database/app.sqlite');
$db->exec('UPDATE lucky_spin_prizes SET is_active = 1');
echo "Updated prizes to active\n";
$stmt = $db->query('SELECT id, name, is_active FROM lucky_spin_prizes');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['id'] . ': ' . $row['name'] . ' (active: ' . $row['is_active'] . ")\n";
}
?>