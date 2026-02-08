<?php

declare(strict_types=1);

try {
  $db = new PDO('sqlite:' . __DIR__ . '/database/app.sqlite');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $password = password_hash('abc123', PASSWORD_DEFAULT);
  $stmt = $db->prepare("UPDATE users SET [password] = ?");
  $stmt->execute([$password]);
  echo "Password updated successfully.";
} catch (PDOException $e) {
  echo "Database error: " . $e->getMessage();
} catch (Exception $e) {
  echo "Error: " . $e->getMessage();
}
