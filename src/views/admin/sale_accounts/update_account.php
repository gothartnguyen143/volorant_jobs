<!-- Add Account Modal -->
<div id="update-account-modal" hidden class="QUERY-modal fixed inset-0 flex items-center justify-center z-90 p-4">
  <div class="QUERY-modal-overlay absolute z-10 inset-0 bg-black/50"></div>

  <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto relative z-20 CSS-styled-scrollbar">
    <div class="p-6 border-b border-gray-200">
      <h2 class="text-xl font-bold text-gray-900">Cập nhật tài khoản sale</h2>
    </div>

    <form id="update-account-form" class="px-6 py-4">
      <div id="pick-avatar--update-section" class="QUERY-at-avatar-input-section mb-4">
        <div class="QUERY-avatar-input-section w-full">
          <label for="avatar-input--update-section" class="block text-sm font-medium text-gray-700 mb-2">Ảnh đại diện (tùy chọn)</label>
          <div class="flex items-center gap-4 relative w-full">
            <input type="file" name="avatar" id="avatar-input--update-section" accept="image/*" class="hidden" />
            <label for="avatar-input--update-section" class="cursor-pointer w-full">
              <div class="flex items-center justify-center flex-col w-full gap-2 p-4 text-center border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="text-sm text-gray-500">Chọn ảnh</span>
                <div class="flex-1">
                  <p class="text-sm text-gray-600">Hỗ trợ: JPG, PNG, WEBP.</p>
                  <button type="button" id="remove-avatar-btn" class="text-sm text-red-600 hover:text-red-800 hidden">Xóa ảnh</button>
                </div>
              </div>
            </label>
          </div>
        </div>
        <div class="QUERY-avatar-preview-section">
          <img src="" alt="Ảnh đại diện" id="avatar-preview-img--update-section" class="w-full object-contain rounded-lg">
          <button type="button" id="cancel-avatar-btn--update-section" class="flex items-center justify-center gap-2 mt-2 px-4 w-full py-1 text-sm text-white bg-red-600 border-2 border-red-600 border-solid hover:bg-transparent hover:text-red-600 rounded transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2-icon lucide-trash-2">
              <path d="M3 6h18" />
              <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
              <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
              <line x1="10" x2="10" y1="11" y2="17" />
              <line x1="14" x2="14" y1="11" y2="17" />
            </svg>
            <span>Hủy ảnh này</span>
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Thư</label>
          <select name="letter" class="w-full px-3 py-2 border border-solid border-gray-300 rounded-lg focus:border-regular-blue-cl focus:outline outline-regular-blue-cl outline-1">
            <option value="Back">Back</option>
            <option value="Có">Có</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Giá</label>
          <input type="text" name="price" class="w-full px-3 py-2 border border-solid border-gray-300 rounded-lg focus:border-regular-blue-cl focus:outline outline-regular-blue-cl outline-1" placeholder="VD: 100000">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Gmail</label>
          <input type="text" name="gmail" class="w-full px-3 py-2 border border-solid border-gray-300 rounded-lg focus:border-regular-blue-cl focus:outline outline-regular-blue-cl outline-1" placeholder="VD: Có">
        </div>
      </div>

      <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả</label>
        <textarea name="description" class="w-full px-3 py-2 border border-solid border-gray-300 rounded-lg focus:border-regular-blue-cl focus:outline outline-regular-blue-cl outline-1" rows="3" placeholder="Mô tả chi tiết về tài khoản..."></textarea>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
          <input type="text" name="status" value="Tốt" class="w-full px-3 py-2 border border-solid border-gray-300 rounded-lg focus:border-regular-blue-cl focus:outline outline-regular-blue-cl outline-1" placeholder="Rảnh, Bận, Check">
        </div>
      </div>
    </form>

    <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
      <button id="update-account-cancel-btn" class="px-4 py-2 text-gray-700 bg-gray-200 hover:scale-110 rounded-lg transition">
        Hủy
      </button>
      <button id="update-account-submit-btn" class="px-4 py-2 bg-gradient-to-r from-regular-from-blue-cl to-regular-to-blue-cl hover:scale-110 text-white rounded-lg transition flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save-icon lucide-save">
          <path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" />
          <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7" />
          <path d="M7 3v4a1 1 0 0 0 1 1h7" />
        </svg>
        <span>Cập nhật tài khoản</span>
      </button>
    </div>
  </div>
</div>