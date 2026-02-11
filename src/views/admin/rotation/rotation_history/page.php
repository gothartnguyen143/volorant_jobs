<?php require_once __DIR__ . '/../../../../utils/Asset.php'; ?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trang quản lý - Lịch sử Vòng quay</title>
  <?php require_once __DIR__ . '/../../../templates/head.php'; ?>
  <link rel="stylesheet" href="<?= queryAssetWithVersion('/pages/admin/rotation/page.css') ?>">
</head>

<body class="min-h-screen bg-gray-50">
  <?php require_once __DIR__ . '/../../../templates/admin/header.php'; ?>

  <div class="w-full max-w-full mx-0 p-4">
    <div class="actions mb-4 flex gap-2">
      <button id="btn-refresh" class="btn">Làm mới</button>
    </div>

    <?php require_once __DIR__ . '/history_table.php'; ?>
  </div>

  <?php require_once __DIR__ . '/../../../templates/bottom_scripts.php'; ?>
</body>

</html>
