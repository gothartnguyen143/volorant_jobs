<div class="min-h-screen bg-gradient-to-br from-regular-from-blue-cl via-regular-via-blue-cl to-regular-to-blue-cl flex items-center justify-center min-[550px]:px-6 py-6 px-4">
  <div class="w-full max-w-md">
    <!-- Header -->
    <div class="text-center mb-8">
      <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-lg rounded-full border border-white/30 shadow-2xl mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-user-icon lucide-shield-user">
          <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
          <path d="M6.376 18.91a6 6 0 0 1 11.249.003" />
          <circle cx="12" cy="11" r="4" />
        </svg>
      </div>
      <h1 class="text-3xl font-bold text-black mb-2">Admin Login</h1>
      <p class="text-black/80">Đăng nhập vào hệ thống quản trị</p>
    </div>

    <!-- Login Form -->
    <div class="bg-white/10 backdrop-blur-lg rounded-3xl min-[550px]:p-8 p-6 shadow-2xl border border-white/20">
      <form id="login-form" class="space-y-6">
        <!-- Username Field -->
        <div class="space-y-2">
          <label for="username" class="block text-black/90 text-sm font-medium">
            Tên người dùng
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-icon lucide-user">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
              </svg>
            </div>
            <input
              type="text"
              id="username-input"
              class="w-full pl-12 pr-4 py-4 placeholder:text-gray-600 bg-white/10 border border-white/20 rounded-xl text-black placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/40 transition-all duration-300 backdrop-blur-sm"
              placeholder="Nhập tên người dùng" />
          </div>
        </div>

        <!-- Password Field -->
        <div class="space-y-2">
          <label for="password" class="block text-black/90 text-sm font-medium">
            Mật khẩu
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-lock-icon lucide-lock text-black" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
              </svg>
            </div>
            <input
              id="password-input"
              type="password"
              class="w-full pl-12 pr-12 py-4 placeholder:text-gray-600 bg-white/10 border border-white/20 rounded-xl text-black placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/40 transition-all duration-300 backdrop-blur-sm"
              placeholder="Nhập mật khẩu" />
            <div id="hide-show-password-section" class="QUERY-hide-password flex items-center space-x-2 absolute right-3 top-1/2 -translate-y-1/2">
              <button type="button" class="QUERY-hide-password-btn text-black cursor-pointer hover:scale-125 transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-off-icon lucide-eye-off">
                  <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49" />
                  <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242" />
                  <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143" />
                  <path d="m2 2 20 20" />
                </svg>
              </button>
              <button type="button" class="QUERY-show-password-btn text-black cursor-pointer hover:scale-125 transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye">
                  <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Login Button -->
        <button
          type="submit"
          class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-gradient-to-r from-white/20 to-white/10 border border-white/30 rounded-xl text-black font-semibold hover:from-white/30 hover:to-white/20 focus:outline-none focus:ring-2 focus:ring-white/30 transition-all duration-300 backdrop-blur-sm shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-in-icon lucide-log-in">
            <path d="m10 17 5-5-5-5" />
            <path d="M15 12H3" />
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
          </svg>
          Đăng nhập
        </button>
      </form>

      <div class="flex justify-center items-center w-full mt-8 border-t border-white/20 pt-6">
        <a href="/" class="hover:text-black text-gray-600 font-bold flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-move-left-icon lucide-move-left flex" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 8L2 12L6 16" />
            <path d="M2 12H22" />
          </svg>
          <span>Trang chủ</span>
        </a>
      </div>

      <!-- Additional Info -->
      <div class="pt-4">
        <p class="text-black/60 text-sm mb-4 text-center">Chỉ dành cho quản trị viên được ủy quyền</p>
      </div>
    </div>

    <!-- Footer -->
    <div class="text-center mt-8">
      <p class="text-black/60 text-sm">
        © 2025 shopthuevalorantime.com. Tất cả quyền được bảo lưu.<br />
        Thiết kế bởi <a href="https://github.com/tuan03" class="text-white underline">Tuan03</a>
      </p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../templates/layout.php'; ?>