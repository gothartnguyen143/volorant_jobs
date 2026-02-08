<div id="accounts-preview-modal" hidden class="fixed inset-0 z-[99] min-[550px]:px-8 py-8 px-2">
  <div class="QUERY-accounts-preview-overlay fixed inset-0 bg-black/50 z-10"></div>

  <div class="flex flex-col gap-4 relative z-20 h-full">
    <h3 class="text-xl font-bold text-white CSS-small-text-stroke leading-none w-full text-center">Xem trước tài khoản</h3>

    <div class="grow overflow-y-auto overflow-x-auto max-w-full rounded-md">
      <table class="w-full rounded-md">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-3 py-3 text-center text-sm font-medium text-gray-600 uppercase tracking-wider">
              STT
            </th>
            <th class="px-3 py-3 text-center text-sm font-medium text-gray-600 uppercase tracking-wider">
              Avatar
            </th>
            <th class="px-3 py-3 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">
              Tên tài khoản
            </th>
            <th class="px-3 py-3 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">
              Rank
            </th>
            <th class="px-3 py-3 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">
              Mã game
            </th>
            <th class="px-3 py-3 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">
              Trạng thái
            </th>
            <th class="px-3 py-3 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">
              Mô tả
            </th>
            <th class="px-3 py-3 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">
              Loại máy
            </th>
          </tr>
        </thead>

        <tbody id="accounts-preview-table-body" class="bg-white divide-y divide-gray-200 overflow-x-auto min-[1024px]:overflow-x-hidden">
        </tbody>
      </table>
    </div>

    <div class="flex gap-2">
      <button id="cancel-importing-accounts-btn" class="CSS-hover-flash-button font-bold bg-red-600 text-white px-4 py-2 rounded-md w-full flex items-center justify-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-x-icon lucide-x" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 6 6 18" />
          <path d="m6 6 12 12" />
        </svg>
        <span>Hủy</span>
      </button>
      <button id="start-importing-accounts-btn" class="CSS-hover-flash-button font-bold bg-regular-blue-cl text-white px-4 py-2 rounded-md w-full flex items-center justify-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cloud-upload-icon lucide-cloud-upload">
          <path d="M12 13v8" />
          <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242" />
          <path d="m8 17 4-4 4 4" />
        </svg>
        <span>Bắt đầu tải lên</span>
      </button>
    </div>
  </div>
</div>