<div class="min-h-screen bg-gradient-to-br from-regular-from-blue-cl via-regular-via-blue-cl to-regular-to-blue-cl min-[550px]:px-6 py-6 pb-10 px-2">
  <div class="min-[550px]:max-w-[90%] w-full mx-auto">
    <!-- Header -->
    <div class="text-center mb-8">
      <h1 class="text-4xl font-bold text-black mb-2">Hồ sơ quản trị viên</h1>
    </div>

    <!-- Main Profile Card -->
    <div class="bg-white/10 backdrop-blur-lg rounded-3xl min-[550px]:px-8 py-8 px-2 shadow-2xl border border-white/20">
      <div class="grid lg:grid-cols-3 gap-1">
        <!-- Avatar Section -->
        <div class="lg:col-span-1 text-center">
          <div class="relative inline-block mb-6">
            <div class="w-48 h-48 bg-gradient-to-br from-white/20 to-white/10 rounded-full flex items-center justify-center border-4 border-white/30 shadow-xl">
              <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-user-icon lucide-user w-24 h-24 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                <circle cx="12" cy="7" r="4" />
              </svg>
            </div>
            <div class="absolute -bottom-2 -right-2 w-16 h-16 bg-green-500 rounded-full flex items-center justify-center border-4 border-white shadow-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-shield-icon lucide-shield w-8 h-8 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Information Section -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Personal Information -->
          <div class="bg-white/10 rounded-2xl min-[550px]:p-6 p-4 backdrop-blur-sm border border-white/20">
            <h3 class="text-black font-bold text-xl mb-4 gap-2 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-icon lucide-user">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
              </svg>
              <span>Thông tin cá nhân</span>
            </h3>
            <div class="grid md:grid-cols-2 gap-4">
              <div class="space-y-1">
                <label class="text-black text-sm font-medium">Tên người dùng</label>
                <div class="bg-white/10 rounded-lg border p-3 border-white/40">
                  <input id="username-input" type="text" class="text-black w-full bg-transparent placeholder:text-gray-600" value="<?= $admin['username'] ?>" placeholder="Nhập tên người dùng..." />
                </div>
              </div>
              <div class="space-y-1">
                <label class="text-black text-sm font-medium">Tên đầy đủ</label>
                <div class="bg-white/10 rounded-lg border p-3 border-white/40">
                  <input id="full-name-input" type="text" class="text-black w-full bg-transparent placeholder:text-gray-600" value="<?= $admin['full_name'] ?>" placeholder="Nhập tên đầy đủ..." />
                </div>
              </div>
              <div class="space-y-1">
                <label class="text-black text-sm font-medium">Số điện thoại</label>
                <div class="bg-white/10 rounded-lg border p-3 border-white/40 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-phone-icon lucide-phone w-6 h-6 text-black mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M22 16.92v3a2 2 0 01-2.18 2A19.79 19.79 0 013 5.18 2 2 0 015 3h3a2 2 0 012 1.72 12.05 12.05 0 00.57 2.57 2 2 0 01-.45 2.11L9 10a16 16 0 007 7l.6-.6a2 2 0 012.11-.45 12.05 12.05 0 002.57.57A2 2 0 0122 16.92z" />
                  </svg>
                  <input id="phone-input" type="text" class="text-black w-full bg-transparent placeholder:text-gray-600" value="<?= $admin['phone'] ?>" placeholder="Nhập số điện thoại..." />
                </div>
              </div>
              <div class="space-y-1">
                <label class="text-black text-sm font-medium">Vai trò</label>
                <div class="bg-regular-blue-2 rounded-lg p-3 flex gap-2 items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-shield-alert-icon lucide-shield-alert text-regular-blue-cl" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                    <path d="M12 8v4" />
                    <path d="M12 16h.01" />
                  </svg>
                  <span class="text-regular-blue-cl font-bold w-full bg-transparent"><?= $admin['role'] ?></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Security Information -->
          <div class="bg-white/10 rounded-2xl min-[550px]:p-6 p-4 backdrop-blur-sm border border-white/20">
            <h3 class="text-black font-bold text-xl mb-4 gap-2 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-icon lucide-shield">
                <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
              </svg>
              <span>Bảo mật</span>
            </h3>
            <div class="space-y-1">
              <label class="text-black text-sm font-medium">Mật khẩu</label>
              <div class="relative bg-white/10 rounded-lg border border-white/40 flex items-center justify-between">
                <input id="password-input" name="password" type="password" class="CSS-password-input-patched-styles text-black p-3 pr-6 w-full bg-transparent placeholder:text-gray-600" placeholder="Nhập mật khẩu mới..." />
                <div id="hide-show-password-section" class="QUERY-hide-password flex items-center space-x-2 absolute right-3 top-1/2 -translate-y-1/2">
                  <button class="QUERY-hide-password-btn text-black cursor-pointer hover:scale-125 transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-off-icon lucide-eye-off">
                      <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49" />
                      <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242" />
                      <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143" />
                      <path d="m2 2 20 20" />
                    </svg>
                  </button>
                  <button class="QUERY-show-password-btn text-black cursor-pointer hover:scale-125 transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye">
                      <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Social Links -->
          <div class="bg-white/10 rounded-2xl min-[550px]:p-6 p-4 backdrop-blur-sm border border-white/20">
            <h3 class="text-black font-bold text-xl mb-4 gap-2 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-link-icon lucide-link">
                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
              </svg>
              <span>Liên kết mạng xã hội</span>
            </h3>
            <div class="grid md:grid-cols-2 gap-4">
              <div class="bg-blue-500/20 rounded-lg p-4 border border-blue-400/30">
                <div class="flex items-center space-x-3">
                  <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-facebook-icon lucide-facebook w-6 h-6 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" />
                    </svg>
                  </div>
                  <div class="pb-1 grow relative">
                    <p class="text-sm font-bold">Facebook</p>
                    <input id="facebook-link-input" type="text" placeholder="Nhập link facebook..." class="peer text-black mt-1 w-full placeholder:text-gray-600 bg-transparent <?= $admin['facebook_link'] ? '' : 'text-gray-600 text-sm' ?>" value="<?= $admin['facebook_link'] ?? 'Chưa có facebook link' ?>">
                    <div class="bg-gray-600 w-full h-[1.5px] absolute z-20 bottom-0 left-0 scale-x-0 peer-focus:scale-x-100 transition duration-300"></div>
                    <div class="bg-gray-400 w-full h-[1.5px] absolute z-10 bottom-0 left-0"></div>
                  </div>
                </div>
              </div>
              <div class="bg-green-500/20 rounded-lg p-4 border border-green-400/30">
                <div class="flex items-center space-x-3">
                  <div class="w-12 h-12 bg-green-500 text-black rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 48 48" id="a" fill="#ffffff" stroke="#ffffff" stroke-width="2.88">
                      <g id="SVGRepo_bgCarrier" stroke-width="0" />
                      <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" />
                      <g id="SVGRepo_iconCarrier">
                        <defs>
                          <style>
                            .b {
                              fill: none;
                              stroke: black;
                              stroke-linecap: round;
                              stroke-linejoin: round;
                            }
                          </style>
                        </defs>
                        <polyline class="b" points="12.466 19.2658 19.182 19.2658 12.466 29.4028 19.182 29.4028" />
                        <path class="b" d="m41.517,4.5018l-29.011.0354C3.882,11.9372,1.282,21.9552,8.976,36.4482c.4875,1.7082-.359,3.42-1.9992,5.1283,2.3642.3218,4.7693-.1218,6.863-1.266,13.983,6.27,21.919,2.1805,29.644-2.7323l.0355-31.074c.0013-1.1046-.8931-2.001-1.9977-2.0023-.0015,0-.003,0-.0045,0h0Z" />
                        <path class="b" d="m25.63,26.791c0,1.4425-1.2196,2.6118-2.724,2.6118s-2.724-1.1694-2.724-2.6118v-1.6978c0-1.4425,1.2196-2.6118,2.724-2.6118s2.724,1.1694,2.724,2.6118" />
                        <path class="b" d="m34.7606,22.483h0c1.4987,0,2.7136,1.2149,2.7136,2.7136v1.4948c0,1.4987-1.2149,2.7136-2.7136,2.7136h0c-1.4987,0-2.7136-1.2149-2.7136-2.7136v-1.4948c0-1.4987,1.2149-2.7136,2.7136-2.7136Z" />
                        <line class="b" x1="25.63" y1="29.403" x2="25.63" y2="22.482" />
                        <path class="b" d="m28.311,18.955v9.1434c0,.7214.5848,1.3062,1.3062,1.3062h.3918" />
                      </g>
                    </svg>
                  </div>
                  <div class="pb-1 grow relative">
                    <p class="text-sm font-bold">Zalo</p>
                    <input id="zalo-link-input" type="text" placeholder="Nhập link zalo..." class="peer text-black mt-1 w-full placeholder:text-gray-600 bg-transparent <?= $admin['zalo_link'] ? '' : 'text-gray-600 text-sm' ?>" value="<?= $admin['zalo_link'] ?? 'Chưa có zalo link' ?>">
                    <div class="bg-gray-600 w-full h-[1.5px] absolute z-20 bottom-0 left-0 scale-x-0 peer-focus:scale-x-100 transition duration-300"></div>
                    <div class="bg-gray-400 w-full h-[1.5px] absolute z-10 bottom-0 left-0"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Rules -->
          <div class="bg-white/10 rounded-2xl min-[550px]:p-6 p-4 backdrop-blur-sm border border-white/20">
            <h3 class="text-black font-bold text-xl mb-4 gap-2 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-ruler-icon lucide-pencil-ruler">
                <path d="M13 7 8.7 2.7a2.41 2.41 0 0 0-3.4 0L2.7 5.3a2.41 2.41 0 0 0 0 3.4L7 13" />
                <path d="m8 6 2-2" />
                <path d="m18 16 2-2" />
                <path d="m17 11 4.3 4.3c.94.94.94 2.46 0 3.4l-2.6 2.6c-.94.94-2.46.94-3.4 0L11 17" />
                <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                <path d="m15 5 4 4" />
              </svg>
              <span>Quy định thuê acc</span>
            </h3>
            <div class="space-y-1">
              <label class="text-black text-sm font-medium">Quy định</label>
              <div class="bg-white/10 rounded-lg border border-white/40">
                <textarea name="rules" id="rules-textarea" rows="5" class="text-black p-3 w-full bg-transparent placeholder:text-gray-600 <?= $rules['rent_acc_rules'] ? '' : 'text-gray-600 text-sm' ?>" placeholder="Nhập quy định thuê acc..."><?= $rules['rent_acc_rules'] ?? 'Chưa có quy định thuê acc...' ?></textarea>
              </div>
            </div>
          </div>

          <!-- Commitment -->
          <div class="bg-white/10 rounded-2xl min-[550px]:p-6 p-4 backdrop-blur-sm border border-white/20">
            <h3 class="text-black font-bold text-xl mb-4 gap-2 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-receipt-text-icon lucide-receipt-text">
                <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z" />
                <path d="M14 8H8" />
                <path d="M16 12H8" />
                <path d="M13 16H8" />
              </svg>
              <span>Cam kết mua acc</span>
            </h3>
            <div class="space-y-1">
              <label class="text-black text-sm font-medium">Cam kết</label>
              <div class="bg-white/10 rounded-lg border border-white/40">
                <textarea name="commitment" id="commitment-textarea" rows="5" class="text-black p-3 w-full bg-transparent placeholder:text-gray-600 <?= $rules['commitment'] ? '' : 'text-gray-600 text-sm' ?>" placeholder="Nhập cam kết mua acc..."><?= $rules['commitment'] ?? 'Chưa có cam kết mua acc...' ?></textarea>
              </div>
            </div>
          </div>

          <div class="flex justify-center mt-8">
            <button id="update-profile-btn" class="bg-gradient-to-l from-regular-from-blue-cl to-regular-to-blue-cl hover:scale-105 text-black font-semibold py-3 px-8 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
              <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <span>Cập nhật hồ sơ</span>
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../templates/layout.php'; ?>