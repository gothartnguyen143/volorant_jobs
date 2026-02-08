<?php require_once __DIR__ . '/../../../utils/Asset.php'; ?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trang quản lý - Thuê Acc Valorant</title>
  <?php require_once __DIR__ . '/../../templates/head.php'; ?>
  <link rel="stylesheet" href="<?= queryAssetWithVersion('/pages/admin/manage-game-accounts/page.css') ?>">
</head>

<body class="min-h-screen bg-gray-50">
  <?php require_once __DIR__ . '/../../templates/admin/header.php'; ?>
  <?php require_once __DIR__ . '/main.php'; ?>
  <?php require_once __DIR__ . '/../../templates/bottom_scripts.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js" defer></script>

  <script src="<?= queryAssetWithVersion('/pages/admin/manage-game-accounts/page.js') ?>" type="module" defer></script>
</body>

</html>