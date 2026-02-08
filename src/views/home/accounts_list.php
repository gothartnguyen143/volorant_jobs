<div id="accounts-list-container" class="pb-20 pt-16">
  <div class="mx-auto min-[550px]:px-6 px-2 max-w-[1475px] min-[2200px]:max-w-[1900px]">
    <!-- Section Header -->
    <div class="text-center mb-6">
      <h2 class="text-[1.71em] md:text-[2.29em] font-black text-gray-800 mb-4">
        DANH SÁCH
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-500 to-cyan-500">
          TÀI KHOẢN
        </span>
      </h2>
      <div class="w-24 h-1 bg-gradient-to-r from-sky-400 to-cyan-400 mx-auto rounded-full mb-6"></div>
      <p class="text-gray-600 text-[1.14em] mx-auto">
        Tuyển chọn những tài khoản chất lượng cao.
      </p>
    </div>

    <?php require_once __DIR__ . '/filter_accounts.php'; ?>

    <!-- Account Cards Grid -->
    <div id="accounts-list" class="grid gap-y-10 gap-x-8 grid-cols-1 mt-8">
    </div>

    <div id="load-more-container" class="QUERY-is-more mt-20 mb-[100px] w-full relative">
      <div class="STYLE-animation-loading-shapes"></div>
      <p class="QUERY-app-loading-message w-max text-base font-bold text-sky-400 absolute top-[calc(50%+50px)] left-1/2 -translate-x-1/2 -translate-y-1/2">
        Đang tải tài khoản...
      </p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/rent_now_modal.php'; ?>