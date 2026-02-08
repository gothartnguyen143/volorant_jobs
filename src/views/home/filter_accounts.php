<div class="bg-[#aae3ff] rounded-2xl px-4 py-2 min-[1441px]:px-6 min-[1441px]:py-4 mx-auto text-black shadow-2xl relative">
  <div class="flex items-center justify-center min-[1072px]:flex-nowrap flex-wrap w-full gap-x-8 gap-y-4 max-h-[300px]">
    <div class="flex items-center gap-2 min-w-max">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-[1.43em] h-[1.43em]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="21" x2="14" y1="4" y2="4" />
        <line x1="10" x2="3" y1="4" y2="4" />
        <line x1="21" x2="12" y1="12" y2="12" />
        <line x1="8" x2="3" y1="12" y2="12" />
        <line x1="21" x2="16" y1="20" y2="20" />
        <line x1="12" x2="3" y1="20" y2="20" />
        <line x1="14" x2="14" y1="2" y2="6" />
        <line x1="8" x2="8" y1="10" y2="14" />
        <line x1="16" x2="16" y1="18" y2="22" />
      </svg>
      <span class="text-[1.43em] font-bold">Lọc acc theo:</span>
    </div>

    <!-- Filter items -->
    <div class="grid grid-cols-2 min-[840px]:grid-cols-5 w-full gap-x-8 gap-y-2 max-h-[300px]">
      <!-- Rank -->
      <div id="account-rank-types-container" class="flex-1">
        <p class="text-[1.14em] font-medium mb-1 flex items-center gap-2 pl-1">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-[1.43em] h-[1.43em] text-current fill-current"
            viewBox="0 0 24 24">
            <path
              d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
          </svg>
          <span>Rank</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="Query-active-icon hidden lucide lucide-check-check-icon lucide-check-check" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6 7 17l-5-5" />
            <path d="m22 10-7.5 7.5L13 16" />
          </svg>
        </p>
        <div id="account-rank-types" class="CSS-hover-flash-button cursor-pointer">
          <select id="account-rank-types-select" class="!text-[1em] leading-tight w-full bg-regular-blue-cl cursor-pointer text-white font-bold rounded-lg px-4 py-1.5 text-base focus:outline-none">
            <option value="ALL" class="bg-white text-black">Tất cả loại rank</option>
          </select>
        </div>
      </div>
      <!-- Trạng thái -->
      <div id="account-statuses-container" class="flex-1">
        <p class="text-[1.14em] font-medium mb-1 flex items-center gap-2 pl-1">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-[1.43em] h-[1.43em]"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="lucide lucide-chart-no-axes-column-icon lucide-chart-no-axes-column text-current">
            <line x1="18" x2="18" y1="20" y2="10" />
            <line x1="12" x2="12" y1="20" y2="4" />
            <line x1="6" x2="6" y1="20" y2="14" />
          </svg>
          <span>Trạng thái</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="Query-active-icon hidden lucide lucide-check-check-icon lucide-check-check" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6 7 17l-5-5" />
            <path d="m22 10-7.5 7.5L13 16" />
          </svg>
        </p>
        <div id="account-statuses" class="CSS-hover-flash-button cursor-pointer">
          <select id="account-statuses-select" class="!text-[1em] leading-tight w-full bg-regular-blue-cl cursor-pointer text-white font-bold rounded-lg px-4 py-1.5 text-base focus:outline-none">
            <option value="ALL" class="bg-white text-black">Tất cả trạng thái</option>
            <option value="Rảnh" class="bg-white text-black">Rảnh</option>
            <option value="Bận" class="bg-white text-black">Bận</option>
          </select>
        </div>
      </div>
      <!-- Loại máy -->
      <div id="account-device-types-container" class="flex-1">
        <p class="text-[1.14em] font-medium mb-1 flex items-center gap-2 pl-1">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-[1.43em] h-[1.43em] text-current"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M9.75 17L6 21h12l-3.75-4M3 4h18v10H3z" />
          </svg>
          <span>Loại máy</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="Query-active-icon hidden lucide lucide-check-check-icon lucide-check-check" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6 7 17l-5-5" />
            <path d="m22 10-7.5 7.5L13 16" />
          </svg>
        </p>
        <div id="account-device-types" class="CSS-hover-flash-button cursor-pointer">
          <select id="account-device-types-select" class="!text-[1em] leading-tight w-full bg-regular-blue-cl cursor-pointer text-white font-bold rounded-lg px-4 py-1.5 text-base focus:outline-none">
            <option value="ALL" class="bg-white text-black">Tất cả loại máy</option>
            <option value="Tất cả" class="bg-white text-black">Tất cả</option>
            <option value="Only máy nhà" class="bg-yellow-400 text-black">Only máy nhà</option>
          </select>
        </div>
      </div>
      <!-- Loại acc -->
      <div id="account-types-container" class="flex-1">
        <p class="text-[1.14em] font-medium mb-1 flex items-center gap-2 pl-1">
          <svg
            class="w-[1.2em] h-[1.2em] text-current"
            stroke="currentColor"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <g>
              <path
                d="M7.24 2H5.34C3.15 2 2 3.15 2 5.33V7.23C2 9.41 3.15 10.56 5.33 10.56H7.23C9.41 10.56 10.56 9.41 10.56 7.23V5.33C10.57 3.15 9.42 2 7.24 2Z"
                fill="currentColor"></path>
              <path
                d="M18.6695 2H16.7695C14.5895 2 13.4395 3.15 13.4395 5.33V7.23C13.4395 9.41 14.5895 10.56 16.7695 10.56H18.6695C20.8495 10.56 21.9995 9.41 21.9995 7.23V5.33C21.9995 3.15 20.8495 2 18.6695 2Z"
                fill="currentColor"></path>
              <path
                d="M18.6695 13.4297H16.7695C14.5895 13.4297 13.4395 14.5797 13.4395 16.7597V18.6597C13.4395 20.8397 14.5895 21.9897 16.7695 21.9897H18.6695C20.8495 21.9897 21.9995 20.8397 21.9995 18.6597V16.7597C21.9995 14.5797 20.8495 13.4297 18.6695 13.4297Z"
                fill="currentColor"></path>
              <path
                d="M7.24 13.4297H5.34C3.15 13.4297 2 14.5797 2 16.7597V18.6597C2 20.8497 3.15 21.9997 5.33 21.9997H7.23C9.41 21.9997 10.56 20.8497 10.56 18.6697V16.7697C10.57 14.5797 9.42 13.4297 7.24 13.4297Z"
                fill="currentColor"></path>
            </g>
          </svg>
          <span>Loại acc</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="Query-active-icon hidden lucide lucide-check-check-icon lucide-check-check" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6 7 17l-5-5" />
            <path d="m22 10-7.5 7.5L13 16" />
          </svg>
        </p>
        <div id="account-types" class="CSS-hover-flash-button cursor-pointer">
          <select id="account-types-select" class="!text-[1em] leading-tight w-full bg-regular-blue-cl cursor-pointer text-white font-bold rounded-lg px-4 py-1.5 text-base focus:outline-none">
            <option value="ALL" class="bg-white text-black">Tất cả loại acc</option>
            <option value="Thường" class="bg-white text-black">Thường</option>
            <option value="Đặc biệt" class="bg-white text-black">Đặc biệt</option>
          </select>
        </div>
      </div>
      <!-- Actions -->
      <div class="w-full">
        <p class="text-[1.14em] font-medium mb-1 opacity-0 items-center gap-2 pl-1 min-[840px]:flex hidden">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" class="w-[1.2em] h-[1.2em] text-current">
            <g>
              <path
                d="M8.00386 9.41816C7.61333 9.02763 7.61334 8.39447 8.00386 8.00395C8.39438 7.61342 9.02755 7.61342 9.41807 8.00395L12.0057 10.5916L14.5907 8.00657C14.9813 7.61605 15.6144 7.61605 16.0049 8.00657C16.3955 8.3971 16.3955 9.03026 16.0049 9.42079L13.4199 12.0058L16.0039 14.5897C16.3944 14.9803 16.3944 15.6134 16.0039 16.0039C15.6133 16.3945 14.9802 16.3945 14.5896 16.0039L12.0057 13.42L9.42097 16.0048C9.03045 16.3953 8.39728 16.3953 8.00676 16.0048C7.61624 15.6142 7.61624 14.9811 8.00676 14.5905L10.5915 12.0058L8.00386 9.41816Z"
                fill="currentColor">
              </path>
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M23 12C23 18.0751 18.0751 23 12 23C5.92487 23 1 18.0751 1 12C1 5.92487 5.92487 1 12 1C18.0751 1 23 5.92487 23 12ZM3.00683 12C3.00683 16.9668 7.03321 20.9932 12 20.9932C16.9668 20.9932 20.9932 16.9668 20.9932 12C20.9932 7.03321 16.9668 3.00683 12 3.00683C7.03321 3.00683 3.00683 7.03321 3.00683 12Z"
                fill="currentColor">
              </path>
            </g>
          </svg>
          <span>Hủy lọc</span>
        </p>
        <button id="cancel-all-filters-btn" class="min-[840px]:mt-0 mt-2 flex items-center gap-2 !text-[1em] w-full leading-tight bg-red-600 hover:scale-105 text-white rounded-lg px-4 py-1.5 text-base font-medium transition duration-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-x-icon lucide-x w-[1.29em] h-[1.29em]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6 6 18" />
            <path d="m6 6 12 12" />
          </svg>
          <span>Hủy lọc</span>
        </button>
      </div>
    </div>
  </div>
</div>