<?php require_once __DIR__ . '/../../../utils/Asset.php'; ?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hồ sơ quản trị viên - Thuê Acc Valorant</title>
  <?php require_once __DIR__ . '/../../templates/head.php'; ?>

  <link rel="stylesheet" href="<?= queryAssetWithVersion('/pages/admin/profile/page.css') ?>">
</head>

<body>
  <?php require_once __DIR__ . '/../../templates/admin/header.php'; ?>
  <?php require_once __DIR__ . '/main.php'; ?>
  <?php require_once __DIR__ . '/../../templates/bottom_scripts.php'; ?>
  <script src="<?= queryAssetWithVersion('/pages/admin/profile/page.js') ?>" type="module" defer></script>
</body>

</html>