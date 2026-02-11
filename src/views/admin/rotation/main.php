<div class="w-full max-w-full mx-0 p-4">
  <!-- <h1>Vòng quay may mắn - Quản trị</h1> -->
  <div class="actions mb-4 flex gap-2 items-center">
    <button id="btn-refresh" class="btn">Làm mới</button>
    <button id="btn-add" class="btn btn-primary">Thêm phần thưởng</button>
    <div class="flex gap-2 items-center ml-4">
      <label for="all-turns-input" class="text-sm font-medium">Lượt quay cho tất cả:</label>
      <input id="all-turns-input" class="input w-20" type="number" min="0" value="1">
      <button id="btn-update-all-turns" class="btn btn-secondary">Cập nhật</button>
    </div>
  </div>

  <?php require_once __DIR__ . '/prizes_table.php'; ?>

  <!-- Modal -->
  <div id="prize-modal" class="modal" hidden>
    <form id="prize-form" class="modal-content bg-white p-4 rounded shadow">
      <h3 id="modal-title" class="text-lg font-semibold mb-2">Thêm phần thưởng</h3>

      <div class="grid grid-cols-1 gap-2">
        <input name="name" class="input" placeholder="Tên" required>
        <input name="probability" class="input" placeholder="Tỷ lệ" type="number" step="0.01">
        <input name="quantity" class="input" placeholder="Số lượng (-1 = vô hạn)" type="number" value="-1">
        <label class="flex items-center gap-2"><input name="is_active" type="checkbox" checked> Active</label>
      </div>

      <div class="modal-actions mt-3 flex gap-2 justify-end">
        <button type="submit" class="btn btn-primary">Lưu</button>
        <button type="button" id="btn-cancel" class="btn">Hủy</button>
      </div>
    </form>
  </div>
</div>
