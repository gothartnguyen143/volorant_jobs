<?php

declare(strict_types=1);

session_start();
$db = new PDO('sqlite:' . __DIR__ . '/../../database/app.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$requiredEnv = ['JWT_SECRET', 'JWT_EXPIRATION'];

foreach ($requiredEnv as $key) {
  if (!isset($_ENV[$key]) || trim($_ENV[$key]) === '') {
    echo ">>> Important data does not exist. Stopping server.\n";
    exit(1);
  }
}
