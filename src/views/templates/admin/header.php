<!-- Header -->
<header class="bg-white shadow-sm border-b border-gray-200">
  <div class="mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      <a href="/" class="flex items-center gap-4">
        <h1 class="text-xl font-bold text-regular-blue-cl">shopthuevalorantime.com</h1>
      </a>

      <div class="items-center gap-4 min-[730px]:flex hidden">
        <a href="/admin/sale-accounts" class="flex items-center gap-2 hover:bg-gray-100 rounded-lg p-2 transition duration-300 cursor-pointer">
          <div class="w-8 h-8 bg-gradient-to-r from-regular-from-blue-cl to-regular-to-blue-cl rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="text-white lucide lucide-coins-icon lucide-coins" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="8" cy="8" r="6" />
              <path d="M18.09 10.37A6 6 0 1 1 10.34 18" />
              <path d="M7 6h1v4" />
              <path d="m16.71 13.88.7.71-2.82 2.82" />
            </svg>
          </div>
          <div class="text-sm">
            <span class="font-medium text-gray-700">Sale</span>
          </div>
        </a>
        <a href="/admin/manage-game-accounts" class="flex items-center gap-2 hover:bg-gray-100 rounded-lg p-2 transition duration-300 cursor-pointer">
          <div class="w-8 h-8 bg-gradient-to-r from-regular-from-blue-cl to-regular-to-blue-cl rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-settings-icon lucide-settings text-white" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
              <circle cx="12" cy="12" r="3" />
            </svg>
          </div>
          <div class="text-sm">
            <span class="font-medium text-gray-700">Quản lý acc</span>
          </div>
        </a>

        <a href="/admin/profile" class="flex items-center gap-3 hover:bg-gray-100 rounded-lg p-2 transition duration-300 cursor-pointer">
          <div class="w-8 h-8 bg-gradient-to-r from-regular-from-blue-cl to-regular-to-blue-cl rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-circle-user-icon lucide-circle-user text-white" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10" />
              <circle cx="12" cy="10" r="3" />
              <path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662" />
            </svg>
          </div>
          <div class="text-sm">
            <div class="font-medium text-gray-900"><?php echo htmlspecialchars($admin['full_name'] ?? $admin['username'] ?? 'Admin'); ?></div>
            <div class="text-gray-500">Quản trị viên</div>
          </div>
        </a>

        <button id="logout-btn" class="p-2 text-gray-600 hover:text-red-600 transition-colors" title="Đăng xuất">
          <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-log-out-icon lucide-log-out" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m16 17 5-5-5-5" />
            <path d="M21 12H9" />
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
          </svg>
        </button>
      </div>

      <button id="open-drawer-menu-btn" class="flex items-center p-2 text-sm rounded-lg min-[730px]:hidden text-gray-800 hover:scale-125 transition duration-200">
        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
          <path clip-rule="evenodd" fill-rule="evenodd"
            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
          </path>
        </svg>
      </button>
    </div>
  </div>
</header>

<!-- Drawer menu: chỉ hiện khi được toggle -->
<div id="header-drawer-menu" class="inset-0 fixed z-[99] min-[730px]:hidden" hidden>
  <div class="QUERY-drawer-menu-overlay inset-0 absolute bg-black/50 z-10"></div>
  <!-- Drawer component -->
  <div class="QUERY-drawer-menu-board">
    <div class="px-2 min-w-[300px]">
      <h5 class="text-base text-black uppercase font-bold">Menu</h5>
      <button class="QUERY-close-drawer-menu-btn text-black bg-transparent hover:bg-regular-blue-cl hover:text-white rounded-lg text-sm w-8 h-8 absolute top-2.5 end-2.5 inline-flex items-center justify-center">
        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
        </svg>
      </button>
      <div class="py-4 overflow-y-auto">
        <ul class="space-y-2 font-medium text-gray-600">
          <li>
            <a href="/admin/sale-accounts" class="flex items-center py-2 rounded-lg CSS-neon-text-hover group">
              <span>Sale</span>
            </a>
          </li>
          <li>
            <a href="/admin/manage-game-accounts" class="flex items-center py-2 rounded-lg CSS-neon-text-hover group">
              <span>Quản lý acc</span>
            </a>
          </li>
          <li>
            <a href="/admin/profile" class="flex items-center py-2 rounded-lg CSS-neon-text-hover group">
              <span>Hồ sơ quản trị viên</span>
            </a>
          </li>
          <li>
            <button id="logout-btn--drawer" class="CSS-button-blue-line-decoration w-full py-[6px] px-[20px] mt-8">
              <span>Đăng xuất</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-log-out-icon lucide-log-out" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m16 17 5-5-5-5" />
                <path d="M21 12H9" />
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
              </svg>
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>