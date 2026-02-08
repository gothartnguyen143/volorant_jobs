<!-- Main Content -->
<main class="mx-auto min-[550px]:px-4 px-2 py-8 bg-gradient-to-br from-regular-from-blue-cl via-regular-via-blue-cl to-regular-to-blue-cl min-h-[calc(100vh-64px)]">
  <!-- Controls -->
  <div class="bg-white rounded-lg shadow mb-6">
    <div class="min-[550px]:p-6 py-6 px-2 border-b border-gray-200">
      <div class="flex flex-col lg:flex-row gap-4 justify-between">
        <!-- Search and Filters -->
        <div class="flex flex-col sm:flex-row gap-4 grow">
          <div class="flex-1 w-full relative">
            <input
              type="text"
              id="search-input"
              placeholder="Tìm kiếm acc theo mô tả..."
              class="w-full pl-4 pr-10 py-2 border border-solid border-gray-300 rounded-lg focus:border-regular-blue-cl focus:outline outline-2 outline-regular-blue-cl" />
            <button id="search-btn" class="absolute right-0 top-1/2 -translate-y-1/2 h-full px-4 text-white rounded-br-lg rounded-tr-lg bg-gradient-to-r from-regular-from-blue-cl to-regular-to-blue-cl cursor-pointer transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-search-icon lucide-search hover:scale-125 transition duration-200" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m21 21-4.34-4.34" />
                <circle cx="11" cy="11" r="8" />
              </svg>
            </button>
          </div>

          <button
            id="toggle-filters-btn"
            class="px-4 py-2 border border-solid rounded-lg flex items-center gap-2 border-gray-300 text-gray-700 hover:scale-110 transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-funnel-icon lucide-funnel">
              <path d="M10 20a1 1 0 0 0 .553.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.742 1.67l7.225 7.989A2 2 0 0 1 10 14z" />
            </svg>
            <span>Bộ lọc</span>
            <span id="count-applied-filters" class="inline-block px-2 py-[2px] text-sm bg-regular-blue-cl text-white font-semibold rounded-full" hidden>
            </span>
          </button>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2">
          <div class="relative group">
            <button class="bg-regular-blue-cl hover:scale-105 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-plus-icon lucide-plus" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14" />
                <path d="M12 5v14" />
              </svg>
              Thêm tài khoản
            </button>
            <div class="flex flex-col items-center gap-2 bg-transparent absolute left-0 mt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-[opacity,visibility] duration-200 z-10">
              <!-- <button id="import-accounts-from-excel-btn" class="flex items-center gap-2 outline outline-2 outline-white w-max text-left font-bold text-white px-4 py-2 bg-regular-blue-cl hover:scale-110 transition duration-200 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-sheet-icon lucide-sheet" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                  <line x1="3" x2="21" y1="9" y2="9" />
                  <line x1="3" x2="21" y1="15" y2="15" />
                  <line x1="9" x2="9" y1="9" y2="21" />
                  <line x1="15" x2="15" y1="9" y2="21" />
                </svg>
                Thêm bằng Excel
              </button> -->
              <button id="add-new-account-btn" class="flex items-center gap-2 outline outline-2 outline-white w-max text-left font-bold text-white px-4 py-2 bg-regular-blue-cl hover:scale-110 transition duration-200 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-hand-icon lucide-hand" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M18 11V6a2 2 0 0 0-2-2a2 2 0 0 0-2 2" />
                  <path d="M14 10V4a2 2 0 0 0-2-2a2 2 0 0 0-2 2v2" />
                  <path d="M10 10.5V6a2 2 0 0 0-2-2a2 2 0 0 0-2 2v8" />
                  <path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15" />
                </svg>
                Thêm thủ công
              </button>
            </div>
          </div>

          <!-- <div class="relative group">
            <button class="bg-gray-600 hover:scale-105 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-download-icon lucide-download" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 15V3" />
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                <path d="m7 10 5 5 5-5" />
              </svg>
              Xuất dữ liệu
              <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-chevron-down-icon lucide-chevron-down" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m6 9 6 6 6-6" />
              </svg>
            </button>
            <div class="flex flex-col items-center gap-2 bg-transparent absolute right-0 mt-2 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-[opacity,visibility] duration-200 z-10">
              <button id="export-accounts-table-to-excel-btn" class="flex items-center gap-2 outline outline-2 outline-white w-max text-left font-bold text-white px-4 py-2 bg-gray-600 hover:scale-110 transition duration-200 rounded-md">
                <svg xmlns=" http://www.w3.org/2000/svg" class="lucide lucide-sheet-icon lucide-sheet text-white" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                  <line x1="3" x2="21" y1="9" y2="9" />
                  <line x1="3" x2="21" y1="15" y2="15" />
                  <line x1="9" x2="9" y1="9" y2="21" />
                  <line x1="15" x2="15" y1="9" y2="21" />
                </svg>
                Xuất Excel
              </button>
            </div>
          </div> -->
        </div>
      </div>

      <?php require_once __DIR__ . '/filter_accounts.php'; ?>
    </div>

    <?php require_once __DIR__ . '/accounts_table.php'; ?>
  </div>

  <!-- View More Button -->
  <div id="load-more-container" class="QUERY-is-more flex justify-center mt-12 mb-6 w-full">
    <button id="load-more-btn" class="QUERY-load-more-btn CSS-button-blue-line-decoration rounded-lg text-white font-bold py-2 px-4">
      XEM THÊM TÀI KHOẢN
    </button>
    <p class="QUERY-no-more-text text-gray-600 text-base font-bold">Không còn tài khoản nào.</p>
  </div>
</main>

<?php require_once __DIR__ . '/../../templates/btn_scroll_to_bottom.php'; ?>

<?php require_once __DIR__ . '/add_new_account.php'; ?>
<?php require_once __DIR__ . '/update_account.php'; ?>
<?php require_once __DIR__ . '/delete_account.php'; ?>
<?php require_once __DIR__ . '/accounts_preview.php'; ?>

<?php require_once __DIR__ . '/../../templates/layout.php'; ?>