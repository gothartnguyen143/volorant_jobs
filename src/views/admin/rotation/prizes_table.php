<?php
// Server-side table rendering for rotation prizes
// Expects $db (PDO) to be available from config included earlier
/** @var PDO $db */
if (!isset($db)) {
  // Try to include the application's config which creates $db
  $candidates = [
    __DIR__ . '/../../../config/config.php',               // src/config/config.php
    __DIR__ . '/../../../../src/config/config.php',        // alternative path
    __DIR__ . '/../../../../config/config.php',           // alternative path
  ];
  foreach ($candidates as $c) {
    if (file_exists($c)) { include_once $c; break; }
  }

  // Fallback: create a local PDO connection directly to database/app.sqlite
  if (!isset($db)) {
    $dbFile = __DIR__ . '/../../../../database/app.sqlite';
    if (!file_exists($dbFile)) {
      $dbFile = __DIR__ . '/../../../database/app.sqlite';
    }
    if (file_exists($dbFile)) {
      $db = new PDO('sqlite:' . $dbFile);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
  }
}

try {
  $stmt = $db->prepare('SELECT * FROM lucky_spin_prizes ORDER BY id ASC');
  $stmt->execute();
  $prizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $prizes = [];
}
?>

<div class="overflow-x-auto">
  <table id="prizes-table" class="w-full border-collapse">
    <thead class="bg-gray-50">
      <tr class="border-b">
        <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">STT</th>
        <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Tên</th>
        <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Tỷ lệ(%)</th>
        <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Số lượng</th>
        <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Kích hoạt</th>
        <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Hành động</th>
      </tr>
    </thead>

    <tbody id="prizes-table-body" class="bg-white divide-y">
      <?php $stt = 1; foreach ($prizes as $p): ?>
        <tr class="hover:bg-gray-50">
          <td class="px-3 py-3 text-sm text-gray-900"><?= $stt++ ?></td>
          <td class="px-3 py-3 text-sm text-gray-900"><?= htmlspecialchars($p['name']) ?></td>
          <td class="px-3 py-3 text-sm text-gray-900"><?= htmlspecialchars($p['probability']) ?></td>
          <td class="px-3 py-3 text-sm text-gray-900"><?= $p['quantity'] === null || intval($p['quantity']) === -1 ? '∞' : intval($p['quantity']) ?></td>
          <td class="px-3 py-3 text-sm text-gray-900">
            <label class="inline-flex items-center gap-2 toggle-switch">
              <input type="checkbox" data-id="<?= htmlspecialchars($p['id']) ?>" class="toggle-active" <?= $p['is_active'] ? 'checked' : '' ?> />
              <span class="switch-track" aria-hidden="true"></span>
            </label>
          </td>
          <td class="px-3 py-3 text-sm text-gray-900">
            <div class="flex gap-2 items-center">
              <button data-id="<?= htmlspecialchars($p['id']) ?>" class="btn-edit icon-btn" title="Sửa">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9" /><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
              </button>
              <button data-id="<?= htmlspecialchars($p['id']) ?>" class="btn-del icon-btn" title="Xóa">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/></svg>
              </button>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
