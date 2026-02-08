<!-- Delete Confirmation Modal -->
<div id="delete-account-modal" hidden class="QUERY-modal fixed inset-0 flex items-center justify-center z-90 p-4">
  <div class="QUERY-modal-overlay absolute z-10 inset-0 bg-black/50"></div>

  <div class="bg-white rounded-lg max-w-md w-full p-6 relative z-20">
    <div class="flex items-center gap-4 mb-4">
      <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-triangle-alert-icon lucide-triangle-alert text-red-600" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3" />
          <path d="M12 9v4" />
          <path d="M12 17h.01" />
        </svg>
      </div>
      <div>
        <h3 class="text-lg font-medium text-gray-900">Xác nhận xóa</h3>
        <p class="text-sm text-gray-500">Bạn có chắc chắn muốn xóa tài khoản <span id="delete-account-name"></span>?</p>
      </div>
    </div>

    <div class="flex justify-end gap-3">
      <button id="delete-account-cancel-button" class="px-4 py-2 text-gray-700 bg-gray-200 hover:scale-110 rounded-lg transition">
        Hủy
      </button>
      <button id="delete-account-confirm-button" class="px-4 py-2 bg-red-600 hover:scale-110 text-white rounded-lg transition">
        Xóa
      </button>
    </div>
  </div>
</div>